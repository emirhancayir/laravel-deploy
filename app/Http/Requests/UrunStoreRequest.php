<?php

namespace App\Http\Requests;

use App\Models\YasakliKelime;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UrunStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->saticiMi();
    }

    protected function prepareForValidation(): void
    {
        // Geçici dosyası olmayan resimleri filtrele - güvenli şekilde
        try {
            if ($this->hasFile('resimler')) {
                $validFiles = [];
                foreach ($this->file('resimler') as $file) {
                    try {
                        if ($file && $file->isValid() && file_exists($file->getPathname())) {
                            $validFiles[] = $file;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                if (empty($validFiles)) {
                    $this->files->remove('resimler');
                } else {
                    $this->files->set('resimler', $validFiles);
                }
            }
        } catch (\Exception $e) {
            // Dosya erişim hatası - dosyaları temizle
            $this->files->remove('resimler');
        }
    }

    public function rules(): array
    {
        return [
            'urun_adi' => ['required', 'string', 'max:255'],
            'aciklama' => ['nullable', 'string', 'max:1000'],
            'fiyat' => ['required', 'numeric', 'min:0.01', 'max:99999'],
            'stok' => ['required', 'integer', 'min:0'],
            'kategori_id' => ['nullable', 'exists:kategoriler,id'],
            'durum' => ['required', 'in:aktif,pasif'],
            'il_id' => ['required', 'exists:iller,id'],
            'ilce_id' => ['required', 'exists:ilceler,id'],
            'mahalle_id' => ['nullable', 'exists:mahalleler,id'],
            'adres_detay' => ['nullable', 'string', 'max:200'],
            // Resim validasyonu controller'da yapılıyor (temp file sorunu nedeniyle)
        ];
    }

    public function messages(): array
    {
        return [
            'urun_adi.required' => 'Ürün adı zorunludur.',
            'urun_adi.max' => 'Ürün adı en fazla 255 karakter olabilir.',
            'aciklama.max' => 'Açıklama en fazla 1000 karakter olabilir.',
            'adres_detay.max' => 'Adres detayı en fazla 200 karakter olabilir.',
            'fiyat.required' => 'Fiyat zorunludur.',
            'fiyat.numeric' => 'Geçerli bir fiyat girin.',
            'fiyat.min' => 'Fiyat 0\'dan büyük olmalıdır.',
            'fiyat.max' => 'Fiyat en fazla 99.999 TL olabilir.',
            'stok.required' => 'Stok miktarı zorunludur.',
            'stok.integer' => 'Stok miktarı tam sayı olmalıdır.',
            'stok.min' => 'Stok miktarı negatif olamaz.',
            'kategori_id.exists' => 'Seçilen kategori bulunamadı.',
            'durum.in' => 'Geçersiz durum seçimi.',
            'resimler.*.image' => 'Dosya bir resim olmalıdır.',
            'resimler.*.mimes' => 'Resim formatı JPEG, PNG, GIF veya WebP olmalıdır.',
            'resimler.*.max' => 'Resim boyutu en fazla 10MB olabilir.',
            'il_id.required' => 'Lütfen bir il seçin.',
            'il_id.exists' => 'Seçilen il bulunamadı.',
            'ilce_id.required' => 'Lütfen bir ilçe seçin.',
            'ilce_id.exists' => 'Seçilen ilçe bulunamadı.',
            'mahalle_id.exists' => 'Seçilen mahalle bulunamadı.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Urun adi kontrolu
            if ($this->urun_adi) {
                $kontrol = YasakliKelime::metinGecerliMi($this->urun_adi, 'urun_adi');
                if (!$kontrol['gecerli']) {
                    $validator->errors()->add('urun_adi', 'Ürün adı uygunsuz içerik barındırıyor.');
                }
            }

            // Aciklama kontrolu
            if ($this->aciklama) {
                $kontrol = YasakliKelime::metinGecerliMi($this->aciklama, 'urun_aciklama');
                if (!$kontrol['gecerli']) {
                    $validator->errors()->add('aciklama', 'Açıklama uygunsuz içerik barındırıyor.');
                }

                // Telefon numarasi kontrolu
                if (preg_match('/(\+90|0)?\s*[5][0-9]{2}\s*[0-9]{3}\s*[0-9]{2}\s*[0-9]{2}/', $this->aciklama)) {
                    $validator->errors()->add('aciklama', 'Açıklamada telefon numarası paylaşamazsınız.');
                }

                // Email kontrolu
                if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $this->aciklama)) {
                    $validator->errors()->add('aciklama', 'Açıklamada e-posta adresi paylaşamazsınız.');
                }
            }
        });
    }
}
