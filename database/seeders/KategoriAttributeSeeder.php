<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\KategoriAttribute;
use Illuminate\Database\Seeder;

class KategoriAttributeSeeder extends Seeder
{
    public function run(): void
    {
        // Vasita Kategorisi
        $vasita = Kategori::firstOrCreate(
            ['kategori_adi' => 'Vasita'],
            ['aciklama' => 'Otomobil, motosiklet ve diger tasitlar']
        );

        $vasitaAttributes = [
            [
                'attribute_adi' => 'marka',
                'label' => 'Marka',
                'tip' => 'select',
                'secenekler' => ['Audi', 'BMW', 'Citroen', 'Dacia', 'Fiat', 'Ford', 'Honda', 'Hyundai', 'Kia', 'Mercedes', 'Nissan', 'Opel', 'Peugeot', 'Renault', 'Seat', 'Skoda', 'Toyota', 'Volkswagen', 'Volvo', 'Diger'],
                'zorunlu' => true,
                'sira' => 1,
            ],
            [
                'attribute_adi' => 'model',
                'label' => 'Model',
                'tip' => 'text',
                'secenekler' => null,
                'zorunlu' => true,
                'sira' => 2,
            ],
            [
                'attribute_adi' => 'yil',
                'label' => 'Yil',
                'tip' => 'number',
                'secenekler' => null,
                'zorunlu' => true,
                'sira' => 3,
            ],
            [
                'attribute_adi' => 'kilometre',
                'label' => 'Kilometre',
                'tip' => 'number',
                'secenekler' => null,
                'zorunlu' => true,
                'sira' => 4,
            ],
            [
                'attribute_adi' => 'yakit_tipi',
                'label' => 'Yakit Tipi',
                'tip' => 'select',
                'secenekler' => ['Benzin', 'Dizel', 'LPG', 'Benzin + LPG', 'Elektrik', 'Hibrit'],
                'zorunlu' => true,
                'sira' => 5,
            ],
            [
                'attribute_adi' => 'vites',
                'label' => 'Vites',
                'tip' => 'select',
                'secenekler' => ['Manuel', 'Otomatik', 'Yari Otomatik'],
                'zorunlu' => true,
                'sira' => 6,
            ],
            [
                'attribute_adi' => 'kasa_tipi',
                'label' => 'Kasa Tipi',
                'tip' => 'select',
                'secenekler' => ['Sedan', 'Hatchback', 'Station Wagon', 'SUV', 'Coupe', 'Cabrio', 'MPV', 'Pick-up'],
                'zorunlu' => false,
                'sira' => 7,
            ],
            [
                'attribute_adi' => 'motor_hacmi',
                'label' => 'Motor Hacmi',
                'tip' => 'text',
                'secenekler' => null,
                'zorunlu' => false,
                'sira' => 8,
            ],
            [
                'attribute_adi' => 'renk',
                'label' => 'Renk',
                'tip' => 'select',
                'secenekler' => ['Beyaz', 'Siyah', 'Gri', 'Gumus', 'Lacivert', 'Mavi', 'Kirmizi', 'Bordo', 'Yesil', 'Kahverengi', 'Bej', 'Turuncu', 'Sari', 'Mor', 'Diger'],
                'zorunlu' => false,
                'sira' => 9,
            ],
        ];

        foreach ($vasitaAttributes as $attr) {
            KategoriAttribute::updateOrCreate(
                ['kategori_id' => $vasita->id, 'attribute_adi' => $attr['attribute_adi']],
                $attr
            );
        }

        // Elektronik Kategorisi
        $elektronik = Kategori::firstOrCreate(
            ['kategori_adi' => 'Elektronik'],
            ['aciklama' => 'Bilgisayar, telefon ve elektronik cihazlar']
        );

        $elektronikAttributes = [
            [
                'attribute_adi' => 'marka',
                'label' => 'Marka',
                'tip' => 'select',
                'secenekler' => ['Apple', 'Samsung', 'Huawei', 'Xiaomi', 'Lenovo', 'HP', 'Dell', 'Asus', 'Acer', 'MSI', 'Monster', 'LG', 'Sony', 'Casper', 'Diger'],
                'zorunlu' => true,
                'sira' => 1,
            ],
            [
                'attribute_adi' => 'model',
                'label' => 'Model',
                'tip' => 'text',
                'secenekler' => null,
                'zorunlu' => true,
                'sira' => 2,
            ],
            [
                'attribute_adi' => 'islemci',
                'label' => 'Islemci',
                'tip' => 'text',
                'secenekler' => null,
                'zorunlu' => false,
                'sira' => 3,
            ],
            [
                'attribute_adi' => 'ram',
                'label' => 'RAM',
                'tip' => 'select',
                'secenekler' => ['2GB', '4GB', '6GB', '8GB', '12GB', '16GB', '32GB', '64GB', '128GB'],
                'zorunlu' => false,
                'sira' => 4,
            ],
            [
                'attribute_adi' => 'depolama',
                'label' => 'Depolama',
                'tip' => 'select',
                'secenekler' => ['32GB', '64GB', '128GB', '256GB', '512GB', '1TB', '2TB'],
                'zorunlu' => false,
                'sira' => 5,
            ],
            [
                'attribute_adi' => 'ekran_boyutu',
                'label' => 'Ekran Boyutu',
                'tip' => 'text',
                'secenekler' => null,
                'zorunlu' => false,
                'sira' => 6,
            ],
            [
                'attribute_adi' => 'garanti',
                'label' => 'Garanti',
                'tip' => 'select',
                'secenekler' => ['Yok', '3 Ay', '6 Ay', '1 Yil', '2 Yil', '3 Yil'],
                'zorunlu' => false,
                'sira' => 7,
            ],
            [
                'attribute_adi' => 'durum',
                'label' => 'Urun Durumu',
                'tip' => 'select',
                'secenekler' => ['Sifir', 'Ikinci El - Cok Iyi', 'Ikinci El - Iyi', 'Ikinci El - Orta'],
                'zorunlu' => true,
                'sira' => 8,
            ],
        ];

        foreach ($elektronikAttributes as $attr) {
            KategoriAttribute::updateOrCreate(
                ['kategori_id' => $elektronik->id, 'attribute_adi' => $attr['attribute_adi']],
                $attr
            );
        }

        $this->command->info('Kategori attributelari basariyla eklendi!');
        $this->command->info('- Vasita: ' . count($vasitaAttributes) . ' attribute');
        $this->command->info('- Elektronik: ' . count($elektronikAttributes) . ' attribute');
    }
}
