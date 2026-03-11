<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MahallelerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Önce mahalle_db_fixed.sql'den ilce eşleşmesini al
        $ilceMapping = $this->buildIlceMapping();

        if (empty($ilceMapping)) {
            $this->command->error('İlçe eşleşmesi oluşturulamadı!');
            return;
        }

        $this->command->info('İlçe eşleşmesi oluşturuldu: ' . count($ilceMapping) . ' ilçe');

        $sqlFiles = [
            base_path('mahalleler_1.sql'),
            base_path('mahalleler_2.sql'),
            base_path('mahalleler_3.sql'),
            base_path('mahalleler_4.sql'),
        ];

        // Önce mevcut verileri temizle
        DB::table('mahalleler')->truncate();

        $insertCount = 0;
        $skippedCount = 0;
        $batch = [];
        $batchSize = 500;

        foreach ($sqlFiles as $sqlFile) {
            if (!file_exists($sqlFile)) {
                $this->command->warn("Dosya bulunamadı: $sqlFile");
                continue;
            }

            $this->command->info("İşleniyor: " . basename($sqlFile));

            $handle = fopen($sqlFile, 'r');
            if (!$handle) {
                $this->command->error("Dosya açılamadı: $sqlFile");
                continue;
            }

            while (($line = fgets($handle)) !== false) {
                $line = trim($line);

                // INSERT INTO mahalleler VALUES (id, il_id, ilce_id, semt_id, "mahalle_adi", "posta_kodu");
                if (preg_match('/INSERT INTO mahalleler VALUES \((\d+),(\d+),(\d+),(\d+),"([^"]+)","([^"]+)"\);/', $line, $matches)) {
                    $sqlIlceId = (int)$matches[3];

                    // SQL ilce_id'yi Laravel ilce_id'ye dönüştür
                    if (!isset($ilceMapping[$sqlIlceId])) {
                        $skippedCount++;
                        continue;
                    }

                    $laravelIlceId = $ilceMapping[$sqlIlceId];

                    $batch[] = [
                        'ilce_id' => $laravelIlceId,
                        'mahalle_adi' => $matches[5],
                        'posta_kodu' => $matches[6],
                    ];

                    if (count($batch) >= $batchSize) {
                        DB::table('mahalleler')->insert($batch);
                        $insertCount += count($batch);
                        $batch = [];

                        if ($insertCount % 5000 === 0) {
                            $this->command->info("  $insertCount kayıt eklendi...");
                        }
                    }
                }
            }

            fclose($handle);
        }

        // Kalan kayıtları ekle
        if (!empty($batch)) {
            DB::table('mahalleler')->insert($batch);
            $insertCount += count($batch);
        }

        $this->command->info("Toplam $insertCount mahalle kaydı eklendi.");
        if ($skippedCount > 0) {
            $this->command->warn("$skippedCount kayıt eşleşme bulunamadığı için atlandı.");
        }
    }

    /**
     * mahalle_db_fixed.sql'den SQL ilce_id -> Laravel ilce_id eşleşmesi oluştur
     */
    private function buildIlceMapping(): array
    {
        $mapping = [];
        $dbFile = base_path('mahalle_db_fixed.sql');

        if (!file_exists($dbFile)) {
            $this->command->error("mahalle_db_fixed.sql bulunamadı!");
            return [];
        }

        $content = file_get_contents($dbFile);

        // ilceler INSERT satırını bul
        if (preg_match('/INSERT INTO `ilceler` VALUES (.+?);/s', $content, $match)) {
            $valuesStr = $match[1];

            // Her bir (id, il_id, 'ilce_adi') çiftini parse et
            preg_match_all('/\((\d+),(\d+),\'([^\']+)\'\)/', $valuesStr, $matches, PREG_SET_ORDER);

            // Laravel DB'deki ilçeleri al
            $laravelIlceler = DB::table('ilceler')
                ->join('iller', 'ilceler.il_id', '=', 'iller.id')
                ->select('ilceler.id', 'ilceler.ilce_adi', 'iller.plaka_kodu')
                ->get()
                ->keyBy(function ($item) {
                    return $item->plaka_kodu . '_' . $this->normalizeIlceAdi($item->ilce_adi);
                });

            // İl ID -> Plaka eşleşmesi (SQL dosyasındaki il_id 1-81 arası)
            // il_id 1 = Adana (plaka 01), il_id 2 = Adıyaman (plaka 02), vs.
            // Ancak il_id sıralaması alfabetik değil, plaka koduna göre
            $ilPlakaMapping = $this->getIlPlakaMapping();

            foreach ($matches as $m) {
                $sqlIlceId = (int)$m[1];
                $sqlIlId = (int)$m[2];
                $ilceAdi = $m[3];

                // SQL il_id'yi plaka koduna dönüştür
                $plaka = $ilPlakaMapping[$sqlIlId] ?? null;
                if (!$plaka) {
                    continue;
                }

                $key = $plaka . '_' . $this->normalizeIlceAdi($ilceAdi);

                if (isset($laravelIlceler[$key])) {
                    $mapping[$sqlIlceId] = $laravelIlceler[$key]->id;
                }
            }
        }

        return $mapping;
    }

    /**
     * SQL dosyasındaki il_id -> plaka kodu eşleşmesi
     * il_id 1-81 arası, plaka 01-81 arası (aynı sırada)
     */
    private function getIlPlakaMapping(): array
    {
        // SQL dosyasındaki il_id plaka koduna eşit değil
        // Örneğin il_id=40 İstanbul (plaka 34)
        // Bu yüzden iller tablosundan eşleşme yapmalıyız

        // Laravel DB'deki iller (plaka_kodu'na göre sıralı, id=plaka)
        $iller = DB::table('iller')->orderBy('id')->pluck('plaka_kodu', 'id');

        // SQL dosyasındaki il sıralaması (alfabetik, plaka sırası değil)
        // Adana, Adıyaman, Afyonkarahisar, Ağrı, Aksaray, Amasya, Ankara, Antalya...
        $sqlIlSirasi = [
            1 => '01',  // Adana
            2 => '02',  // Adıyaman
            3 => '03',  // Afyonkarahisar
            4 => '04',  // Ağrı
            5 => '68',  // Aksaray
            6 => '05',  // Amasya
            7 => '06',  // Ankara
            8 => '07',  // Antalya
            9 => '75',  // Ardahan
            10 => '08', // Artvin
            11 => '09', // Aydın
            12 => '10', // Balıkesir
            13 => '74', // Bartın
            14 => '72', // Batman
            15 => '69', // Bayburt
            16 => '11', // Bilecik
            17 => '12', // Bingöl
            18 => '13', // Bitlis
            19 => '14', // Bolu
            20 => '15', // Burdur
            21 => '16', // Bursa
            22 => '17', // Çanakkale
            23 => '18', // Çankırı
            24 => '19', // Çorum
            25 => '20', // Denizli
            26 => '21', // Diyarbakır
            27 => '81', // Düzce
            28 => '22', // Edirne
            29 => '23', // Elazığ
            30 => '24', // Erzincan
            31 => '25', // Erzurum
            32 => '26', // Eskişehir
            33 => '27', // Gaziantep
            34 => '28', // Giresun
            35 => '29', // Gümüşhane
            36 => '30', // Hakkari
            37 => '31', // Hatay
            38 => '76', // Iğdır
            39 => '32', // Isparta
            40 => '34', // İstanbul
            41 => '35', // İzmir
            42 => '46', // Kahramanmaraş
            43 => '78', // Karabük
            44 => '70', // Karaman
            45 => '36', // Kars
            46 => '37', // Kastamonu
            47 => '38', // Kayseri
            48 => '71', // Kırıkkale
            49 => '39', // Kırklareli
            50 => '40', // Kırşehir
            51 => '79', // Kilis
            52 => '41', // Kocaeli
            53 => '42', // Konya
            54 => '43', // Kütahya
            55 => '44', // Malatya
            56 => '45', // Manisa
            57 => '47', // Mardin
            58 => '33', // Mersin
            59 => '48', // Muğla
            60 => '49', // Muş
            61 => '50', // Nevşehir
            62 => '51', // Niğde
            63 => '52', // Ordu
            64 => '80', // Osmaniye
            65 => '53', // Rize
            66 => '54', // Sakarya
            67 => '55', // Samsun
            68 => '56', // Siirt
            69 => '57', // Sinop
            70 => '58', // Sivas
            71 => '63', // Şanlıurfa
            72 => '73', // Şırnak
            73 => '59', // Tekirdağ
            74 => '60', // Tokat
            75 => '61', // Trabzon
            76 => '62', // Tunceli
            77 => '64', // Uşak
            78 => '65', // Van
            79 => '77', // Yalova
            80 => '66', // Yozgat
            81 => '67', // Zonguldak
        ];

        return $sqlIlSirasi;
    }

    private function normalizeIlceAdi(string $ilceAdi): string
    {
        // Türkçe karakterleri ve boşlukları standartlaştır
        $ilceAdi = mb_strtolower($ilceAdi, 'UTF-8');
        $ilceAdi = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç'], ['i', 'g', 'u', 's', 'o', 'c'], $ilceAdi);
        $ilceAdi = preg_replace('/\s+/', '', $ilceAdi);
        return $ilceAdi;
    }
}
