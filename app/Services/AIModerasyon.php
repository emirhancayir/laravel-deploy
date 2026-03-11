<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIModerasyon
{
    protected string $apiKey;
    protected string $model = 'gpt-4o-mini';
    protected int $cacheDakika = 60; // Ayni metin icin cache suresi

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key') ?? env('OPENAI_API_KEY');
    }

    /**
     * Icerigi AI ile kontrol et
     */
    public function kontrol(string $icerik, string $tip = 'mesaj'): array
    {
        // Bos icerik kontrolu
        if (empty(trim($icerik))) {
            return ['uygun' => true, 'sebep' => null];
        }

        // Cache kontrolu (ayni metin icin tekrar API cagirma)
        $cacheKey = 'ai_moderasyon_' . md5($icerik . $tip);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt($tip)
                    ],
                    [
                        'role' => 'user',
                        'content' => $icerik
                    ]
                ],
                'temperature' => 0,
                'max_tokens' => 150,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $sonuc = $data['choices'][0]['message']['content'] ?? '';

                // JSON parse et
                $json = $this->parseJson($sonuc);

                $result = [
                    'uygun' => $json['uygun'] ?? true,
                    'sebep' => $json['sebep'] ?? null,
                    'kategori' => $json['kategori'] ?? null,
                ];

                // Cache'e kaydet
                Cache::put($cacheKey, $result, now()->addMinutes($this->cacheDakika));

                return $result;
            }

            // API hatasi - guvenli tarafta kal, izin ver
            Log::warning('AI Moderasyon API hatasi', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return ['uygun' => true, 'sebep' => null, 'hata' => 'API hatası'];

        } catch (\Exception $e) {
            Log::error('AI Moderasyon exception', ['error' => $e->getMessage()]);
            return ['uygun' => true, 'sebep' => null, 'hata' => $e->getMessage()];
        }
    }

    /**
     * Tip'e gore system prompt dondur
     */
    protected function getSystemPrompt(string $tip): string
    {
        $basePrompt = "Sen bir Türkçe içerik moderatörüsün. E-ticaret platformu ZAMASON için içerikleri kontrol ediyorsun.

Verilen metni analiz et ve şunları kontrol et:
1. Küfür, hakaret, argo kelimeler
2. Cinsel veya müstehcen içerik
3. Nefret söylemi, ırkçılık, ayrımcılık
4. Tehdit veya şiddet içeren ifadeler
5. Spam veya anlamsız tekrar eden içerik";

        if ($tip === 'mesaj') {
            $basePrompt .= "
6. Telefon numarası (05XX XXX XX XX formatında)
7. E-posta adresi
8. Sosyal medya hesapları (WhatsApp, Instagram, Telegram, Facebook)
9. IBAN, banka hesabı veya para transferi talebi
10. Site dışına yönlendirme girişimi";
        }

        $basePrompt .= "

SADECE JSON formatında yanıt ver, başka hiçbir şey yazma:
{\"uygun\": true/false, \"sebep\": \"kısa açıklama veya null\", \"kategori\": \"küfür/spam/iletisim/diger veya null\"}

Örnekler:
- 'Bu ürün çok güzel' -> {\"uygun\": true, \"sebep\": null, \"kategori\": null}
- 'Aptal mısın sen' -> {\"uygun\": false, \"sebep\": \"Hakaret içeriyor\", \"kategori\": \"küfür\"}
- 'WhatsApp'tan yaz 0532...' -> {\"uygun\": false, \"sebep\": \"Site dışı iletişim girişimi\", \"kategori\": \"iletisim\"}";

        return $basePrompt;
    }

    /**
     * JSON string'i parse et
     */
    protected function parseJson(string $sonuc): array
    {
        // Sadece JSON kismini al
        if (preg_match('/\{.*\}/s', $sonuc, $matches)) {
            $sonuc = $matches[0];
        }

        $json = json_decode($sonuc, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }

        // Parse edilemezse guvenli deger dondur
        return ['uygun' => true, 'sebep' => null];
    }

    /**
     * Hizli kontrol - sadece OpenAI Moderation API kullan (ucretsiz)
     */
    public function hizliKontrol(string $icerik): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(5)->post('https://api.openai.com/v1/moderations', [
                'input' => $icerik,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = $data['results'][0] ?? [];

                $flagged = $results['flagged'] ?? false;
                $categories = $results['categories'] ?? [];

                $uygunsuzKategoriler = array_keys(array_filter($categories));

                return [
                    'uygun' => !$flagged,
                    'sebep' => $flagged ? 'Uygunsuz içerik tespit edildi: ' . implode(', ', $uygunsuzKategoriler) : null,
                    'kategoriler' => $uygunsuzKategoriler,
                ];
            }

            return ['uygun' => true, 'sebep' => null];

        } catch (\Exception $e) {
            Log::error('AI Hizli Moderasyon exception', ['error' => $e->getMessage()]);
            return ['uygun' => true, 'sebep' => null];
        }
    }

    /**
     * Hibrit kontrol - once hizli, sonra detayli
     */
    public function hibritKontrol(string $icerik, string $tip = 'mesaj'): array
    {
        // Once hizli kontrol (ucretsiz)
        $hizli = $this->hizliKontrol($icerik);

        if (!$hizli['uygun']) {
            return $hizli;
        }

        // Hizli kontrolden gecti, detayli kontrol yap (ucretli ama ucuz)
        return $this->kontrol($icerik, $tip);
    }
}
