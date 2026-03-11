<?php

namespace App\Services;

use App\Models\Odeme;
use App\Models\User;
use App\Models\Urun;
use Iyzipay\Options;
use Iyzipay\Model\Locale;
use Iyzipay\Model\Currency;
use Iyzipay\Model\PaymentGroup;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\Address;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\CheckoutFormInitialize;
use Iyzipay\Model\CheckoutForm;
use Iyzipay\Request\CreateCheckoutFormInitializeRequest;
use Iyzipay\Request\RetrieveCheckoutFormRequest;

class IyzicoService
{
    private Options $options;

    public function __construct()
    {
        $this->options = new Options();
        $this->options->setApiKey(config('services.iyzico.api_key'));
        $this->options->setSecretKey(config('services.iyzico.secret_key'));
        $this->options->setBaseUrl(config('services.iyzico.base_url'));
    }

    /**
     * Checkout formu baslat
     */
    public function initializeCheckoutForm(
        Odeme $odeme,
        User $buyer,
        Urun $product,
        string $callbackUrl
    ): ?CheckoutFormInitialize {
        $request = new CreateCheckoutFormInitializeRequest();
        $request->setLocale(Locale::TR);
        $request->setConversationId($odeme->conversation_id);
        // price: Sepet öğelerinin toplam tutarı (ürün + kargo)
        // paidPrice: Müşteriden alınacak toplam tutar
        $request->setPrice(number_format($odeme->toplam_tutar, 2, '.', ''));
        $request->setPaidPrice(number_format($odeme->toplam_tutar, 2, '.', ''));
        $request->setCurrency(Currency::TL);
        $request->setBasketId('ODEME-' . $odeme->id);
        $request->setPaymentGroup(PaymentGroup::PRODUCT);
        $request->setCallbackUrl($callbackUrl);
        $request->setEnabledInstallments([1, 2, 3, 6]);

        // Alici bilgileri
        $buyerObj = new Buyer();
        $buyerObj->setId((string) $buyer->id);
        $buyerObj->setName($buyer->ad);
        $buyerObj->setSurname($buyer->soyad);
        $buyerObj->setEmail($buyer->email);
        $buyerObj->setGsmNumber($this->formatPhone($buyer->telefon));
        $buyerObj->setIdentityNumber('11111111111'); // TC Kimlik (zorunlu alan)
        $buyerObj->setRegistrationAddress($buyer->adres ?? 'Turkiye');
        $buyerObj->setCity('Istanbul');
        $buyerObj->setCountry('Turkey');
        $buyerObj->setIp(request()->ip());
        $request->setBuyer($buyerObj);

        // Teslimat adresi
        $shippingAddress = new Address();
        $shippingAddress->setContactName($buyer->ad_soyad);
        $shippingAddress->setCity($odeme->teslimatIl?->il_adi ?? 'Istanbul');
        $shippingAddress->setCountry('Turkey');
        $shippingAddress->setAddress($odeme->teslimat_adres ?? $buyer->adres ?? 'Turkiye');
        $request->setShippingAddress($shippingAddress);

        // Fatura adresi
        $billingAddress = new Address();
        $billingAddress->setContactName($buyer->ad_soyad);
        $billingAddress->setCity('Istanbul');
        $billingAddress->setCountry('Turkey');
        $billingAddress->setAddress($buyer->adres ?? 'Turkiye');
        $request->setBillingAddress($billingAddress);

        // Sepet urunleri
        $basketItems = [];

        // Urun
        $item = new BasketItem();
        $item->setId('URUN-' . $product->id);
        $item->setName(mb_substr($product->urun_adi, 0, 50));
        $item->setCategory1($product->kategori?->kategori_adi ?? 'Genel');
        $item->setItemType(BasketItemType::PHYSICAL);
        $item->setPrice(number_format($odeme->urun_tutari, 2, '.', ''));
        $basketItems[] = $item;

        // Kargo ucreti (varsa)
        if ($odeme->kargo_tutari > 0) {
            $kargoItem = new BasketItem();
            $kargoItem->setId('KARGO-' . $odeme->id);
            $kargoItem->setName('Kargo Ucreti');
            $kargoItem->setCategory1('Kargo');
            $kargoItem->setItemType(BasketItemType::VIRTUAL);
            $kargoItem->setPrice(number_format($odeme->kargo_tutari, 2, '.', ''));
            $basketItems[] = $kargoItem;
        }

        $request->setBasketItems($basketItems);

        return CheckoutFormInitialize::create($request, $this->options);
    }

    /**
     * Odeme sonucunu getir
     */
    public function retrieveCheckoutForm(string $token): ?CheckoutForm
    {
        $request = new RetrieveCheckoutFormRequest();
        $request->setLocale(Locale::TR);
        $request->setToken($token);

        return CheckoutForm::retrieve($request, $this->options);
    }

    /**
     * Marketplace icin odeme onayla
     */
    public function approvePayment(string $paymentTransactionId): bool
    {
        $request = new \Iyzipay\Request\CreateApprovalRequest();
        $request->setLocale(Locale::TR);
        $request->setPaymentTransactionId($paymentTransactionId);

        $approval = \Iyzipay\Model\Approval::create($request, $this->options);

        return $approval->getStatus() === 'success';
    }

    /**
     * Telefon numarasini formatlama
     */
    private function formatPhone(?string $phone): string
    {
        if (!$phone) {
            return '+905000000000';
        }

        // Sadece rakamlari al
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Turkiye kodu ekle
        if (strlen($phone) === 10 && str_starts_with($phone, '5')) {
            return '+90' . $phone;
        }

        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            return '+9' . $phone;
        }

        return '+90' . $phone;
    }

    /**
     * Komisyon hesapla
     * @param float $tutar Toplam tutar
     * @param float|null $komisyonOrani Kategori komisyon orani (null ise varsayilan kullanilir)
     */
    public static function komisyonHesapla(float $tutar, ?float $komisyonOrani = null): array
    {
        // Eger komisyon orani verilmemisse, varsayilani kullan
        if ($komisyonOrani === null) {
            $komisyonOrani = config('zamason.komisyon_orani', 5);
        }

        $komisyonTutari = $tutar * ($komisyonOrani / 100);
        $saticiTutari = $tutar - $komisyonTutari;

        return [
            'komisyon_orani' => $komisyonOrani,
            'komisyon_tutari' => round($komisyonTutari, 2),
            'satici_tutari' => round($saticiTutari, 2),
        ];
    }
}
