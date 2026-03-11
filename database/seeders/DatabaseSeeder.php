<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\User;
use App\Models\Urun;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Kategoriler
        $kategoriler = [
            ['kategori_adi' => 'Elektronik', 'aciklama' => 'Telefon, bilgisayar, tablet ve diğer elektronik ürünler'],
            ['kategori_adi' => 'Giyim', 'aciklama' => 'Erkek, kadın ve çocuk giyim ürünleri'],
            ['kategori_adi' => 'Ev & Yaşam', 'aciklama' => 'Ev dekorasyonu, mobilya ve yaşam ürünleri'],
            ['kategori_adi' => 'Spor & Açık Hava', 'aciklama' => 'Spor ekipmanları ve açık hava ürünleri'],
            ['kategori_adi' => 'Kitap & Kırtasiye', 'aciklama' => 'Kitaplar, defterler ve kırtasiye malzemeleri'],
            ['kategori_adi' => 'Kozmetik', 'aciklama' => 'Makyaj ve kişisel bakım ürünleri'],
            ['kategori_adi' => 'Vasıta', 'aciklama' => 'Otomobil, motosiklet ve diğer taşıtlar'],
        ];

        foreach ($kategoriler as $kat) {
            Kategori::create([
                'kategori_adi' => $kat['kategori_adi'],
                'slug' => \Str::slug($kat['kategori_adi']),
                'aciklama' => $kat['aciklama'],
                'komisyon_orani' => 10,
                'aktif' => true,
            ]);
        }

        // Admin User (password: password123)
        User::create([
            'ad' => 'Admin',
            'soyad' => 'User',
            'email' => 'admin@flavor.com',
            'password' => bcrypt('password123'),
            'telefon' => '05550000000',
            'kullanici_tipi' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        // Super Admin (password: Xk9#mP2$vL7@nQ4!)
        User::create([
            'ad' => 'Super',
            'soyad' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('Xk9#mP2$vL7@nQ4!'),
            'telefon' => '05551111111',
            'kullanici_tipi' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        // Demo Seller (password: 123456)
        $satici = User::create([
            'ad' => 'Demo',
            'soyad' => 'Seller',
            'email' => 'seller@demo.com',
            'password' => bcrypt('123456'),
            'telefon' => '05551234567',
            'kullanici_tipi' => 'satici',
            'email_verified_at' => now(),
        ]);

        // Demo Buyer (password: 123456)
        User::create([
            'ad' => 'Demo',
            'soyad' => 'Buyer',
            'email' => 'buyer@demo.com',
            'password' => bcrypt('123456'),
            'telefon' => '05559876543',
            'kullanici_tipi' => 'alici',
            'email_verified_at' => now(),
        ]);

        // Örnek Ürünler
        $urunler = [
            ['kategori_id' => 1, 'urun_adi' => 'Akıllı Telefon Pro Max', 'aciklama' => 'Son teknoloji akıllı telefon, 256GB hafıza, 8GB RAM', 'fiyat' => 24999.99, 'stok' => 50],
            ['kategori_id' => 1, 'urun_adi' => 'Kablosuz Kulaklık', 'aciklama' => 'Bluetooth 5.0, aktif gürültü önleme, 30 saat pil ömrü', 'fiyat' => 1299.99, 'stok' => 100],
            ['kategori_id' => 2, 'urun_adi' => 'Premium Erkek Ceket', 'aciklama' => 'Kışlık, su geçirmez, rüzgar kesici ceket', 'fiyat' => 899.99, 'stok' => 30],
            ['kategori_id' => 3, 'urun_adi' => 'Modern Masa Lambası', 'aciklama' => 'LED, dokunmatik kontrol, 3 renk modu', 'fiyat' => 349.99, 'stok' => 75],
            ['kategori_id' => 4, 'urun_adi' => 'Yoga Matı Premium', 'aciklama' => 'Kaymaz yüzey, 6mm kalınlık, taşıma askısı dahil', 'fiyat' => 199.99, 'stok' => 120],
            ['kategori_id' => 5, 'urun_adi' => 'Klasik Roman Seti', 'aciklama' => '10 kitaplık dünya klasikleri seti', 'fiyat' => 449.99, 'stok' => 40],
            ['kategori_id' => 1, 'urun_adi' => 'Laptop Pro 15"', 'aciklama' => 'Intel i7, 16GB RAM, 512GB SSD, profesyonel laptop', 'fiyat' => 32999.99, 'stok' => 25],
            ['kategori_id' => 6, 'urun_adi' => 'Cilt Bakım Seti', 'aciklama' => 'Nemlendirici, temizleyici ve serum içeren set', 'fiyat' => 599.99, 'stok' => 60],
        ];

        foreach ($urunler as $urun) {
            Urun::create([
                'satici_id' => $satici->id,
                'kategori_id' => $urun['kategori_id'],
                'urun_adi' => $urun['urun_adi'],
                'aciklama' => $urun['aciklama'],
                'fiyat' => $urun['fiyat'],
                'stok' => $urun['stok'],
                'durum' => 'aktif',
                'onay_durumu' => 'onaylandi',
            ]);
        }

        // Kategori Attribute'larini ekle
        $this->call(KategoriAttributeSeeder::class);
    }
}
