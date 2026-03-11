-- ZAMASON Örnek Ürünler
-- Bu dosyayı import etmeden önce mevcut ürünleri silmek isteyebilirsiniz

-- Önce kategorileri ekleyelim (varsa atla)
INSERT IGNORE INTO kategoriler (id, kategori_adi, aciklama, created_at, updated_at) VALUES
(1, 'Elektronik', 'Telefon, tablet, bilgisayar ve elektronik aksesuarlar', NOW(), NOW()),
(2, 'Giyim', 'Kadın, erkek ve çocuk giyim ürünleri', NOW(), NOW()),
(3, 'Ev & Yaşam', 'Ev dekorasyonu ve yaşam ürünleri', NOW(), NOW()),
(4, 'Spor & Outdoor', 'Spor ekipmanları ve outdoor ürünleri', NOW(), NOW()),
(5, 'Kozmetik', 'Kişisel bakım ve kozmetik ürünleri', NOW(), NOW());

-- Foreign key kontrolünü kapat
SET FOREIGN_KEY_CHECKS = 0;

-- Tüm bağlı tabloları temizle
DELETE FROM odemeler;
DELETE FROM konusmalar;
DELETE FROM mesajlar;
DELETE FROM kargolar;
DELETE FROM favoriler;
DELETE FROM urun_resimleri;
DELETE FROM sepet;
DELETE FROM siparis_detaylari;
DELETE FROM siparisler;
DELETE FROM yorumlar;
DELETE FROM urun_attribute_values;

-- Mevcut ürünleri temizle
DELETE FROM urunler;

-- Foreign key kontrolünü aç
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO urunler (id, satici_id, kategori_id, urun_adi, aciklama, fiyat, eski_fiyat, stok, resim, durum, goruntulenme_sayisi, created_at, updated_at) VALUES
(1, 11, 1, 'iPhone 14 Pro Max 256GB', 'Apple iPhone 14 Pro Max, 256GB dahili hafıza, uzay siyahı renk. Kutusunda, faturalı, 1 yıl garantili.', 54999.00, 62999.00, 5, 'urun_693a821234d22.jpg', 'aktif', 124, NOW(), NOW()),

(2, 11, 1, 'Samsung Galaxy S23 Ultra', 'Samsung Galaxy S23 Ultra 256GB, Phantom Black. Sıfır, kapalı kutu, Türkiye garantili.', 47999.00, 52999.00, 3, 'urun_693a82e4cf4d1.jpg', 'aktif', 89, NOW(), NOW()),

(3, 11, 1, 'MacBook Air M2 256GB', 'Apple MacBook Air M2 çip, 8GB RAM, 256GB SSD. Midnight renk, sıfır ürün.', 42999.00, 47999.00, 2, 'urun_693a82f6c47dd.jpg', 'aktif', 156, NOW(), NOW()),

(4, 11, 2, 'Nike Air Max 270 Erkek', 'Nike Air Max 270 erkek spor ayakkabı. 42-43-44 numara mevcuttur. Orijinal ürün.', 3299.00, 3999.00, 8, 'urun_693a848c412b6.jpg', 'aktif', 67, NOW(), NOW()),

(5, 11, 2, 'Adidas Ultraboost 22', 'Adidas Ultraboost 22 koşu ayakkabısı. Siyah/beyaz renk, 41-42-43 numara mevcut.', 3799.00, 4499.00, 6, 'urun_693a84a0f1a5d.jpg', 'aktif', 45, NOW(), NOW()),

(6, 11, 3, 'Dyson V15 Detect Süpürge', 'Dyson V15 Detect kablosuz süpürge. Lazer teknolojisi ile toz algılama. Sıfır, garantili.', 28999.00, 32999.00, 2, 'urun_69426e89ccccf.jpg', 'aktif', 234, NOW(), NOW()),

(7, 11, 5, 'La Roche-Posay Effaclar Set', 'La Roche-Posay Effaclar cilt bakım seti. Yağlı ve karma ciltler için komple set.', 1299.00, 1599.00, 15, 'urun_6942a1b1ddf84.png', 'aktif', 78, NOW(), NOW()),

(8, 11, 1, 'Sony WH-1000XM5 Kulaklık', 'Sony WH-1000XM5 kablosuz kulaklık. Aktif gürültü engelleme, 30 saat pil ömrü.', 9999.00, 11999.00, 4, 'urun_6943afb07796c.jpg', 'aktif', 167, NOW(), NOW()),

(9, 11, 1, 'iPad Pro 12.9 M2 256GB', 'Apple iPad Pro 12.9 inç M2 çip, 256GB. WiFi + Cellular model, uzay grisi.', 39999.00, 44999.00, 3, 'urun_6943b8cb5c4f8.jpg', 'aktif', 98, NOW(), NOW()),

(10, 11, 1, 'Apple Watch Ultra 2', 'Apple Watch Ultra 2, 49mm titanyum kasa. Turuncu Alpine Loop kordon dahil.', 27999.00, 31999.00, 2, 'urun_6943b8cb5c5f0.jpg', 'aktif', 145, NOW(), NOW()),

(11, 11, 4, 'Garmin Fenix 7X Solar', 'Garmin Fenix 7X Solar akıllı saat. GPS, solar şarj, 28 gün pil ömrü.', 24999.00, 28999.00, 1, 'urun_6943bf5653040.jpg', 'aktif', 112, NOW(), NOW()),

(12, 11, 5, 'Estee Lauder Advanced Night', 'Estee Lauder Advanced Night Repair Serum 50ml. Orijinal, sıfır.', 2799.00, 3299.00, 10, 'urun_6943cb701bd8c.jpeg', 'aktif', 56, NOW(), NOW()),

(13, 11, 5, 'Clinique Moisture Surge', 'Clinique Moisture Surge 100H nemlendirici 75ml. Tüm cilt tipleri için.', 1599.00, 1899.00, 12, 'urun_694b8199a3a24.jpeg', 'aktif', 43, NOW(), NOW()),

(14, 11, 1, 'PlayStation 5 Slim Digital', 'Sony PlayStation 5 Slim Digital Edition. Sıfır, Türkiye garantili.', 17999.00, 19999.00, 4, 'urun_6955308e8f548.png', 'aktif', 289, NOW(), NOW()),

(15, 11, 1, 'Xbox Series X', 'Microsoft Xbox Series X 1TB. Sıfır, kapalı kutu, 2 yıl garanti.', 16999.00, 18999.00, 3, 'urun_6955308e8f57e.png', 'aktif', 178, NOW(), NOW()),

(16, 11, 1, 'Nintendo Switch OLED', 'Nintendo Switch OLED Model beyaz. Sıfır, kutusunda, Türkiye garantili.', 11999.00, 13999.00, 5, 'urun_6955308e8f591.png', 'aktif', 134, NOW(), NOW()),

(17, 11, 2, 'The North Face Erkek Mont', 'The North Face Thermoball Eco erkek mont. Siyah, L-XL beden mevcut.', 5999.00, 7499.00, 4, 'urun_696e08b5f259f.JPG', 'aktif', 67, NOW(), NOW()),

(18, 11, 2, 'Columbia Erkek Outdoor Ceket', 'Columbia erkek outdoor su geçirmez ceket. M-L-XL beden, lacivert renk.', 3499.00, 4299.00, 6, 'urun_696e09507bca6.JPG', 'aktif', 45, NOW(), NOW()),

(19, 11, 4, 'Salomon X Ultra 4 GTX', 'Salomon X Ultra 4 Gore-Tex outdoor ayakkabı. 42-43-44 numara mevcut.', 4999.00, 5999.00, 5, 'urun_696e0b8775e5a.JPG', 'aktif', 89, NOW(), NOW()),

(20, 11, 2, 'Levi''s 501 Original Fit Jean', 'Levi''s 501 Original Fit erkek jean pantolon. Mavi, 32-33-34 beden.', 1899.00, 2299.00, 10, 'urun_696e117296916.JPG', 'aktif', 56, NOW(), NOW()),

(21, 11, 2, 'Tommy Hilfiger Erkek Polo', 'Tommy Hilfiger slim fit erkek polo tişört. Beyaz, M-L-XL beden.', 1299.00, 1599.00, 15, 'urun_696e1d94467fb.JPG', 'aktif', 34, NOW(), NOW()),

(22, 11, 3, 'Philips Airfryer XXL', 'Philips Airfryer XXL Premium HD9867. 7.3L kapasite, dijital ekran.', 8999.00, 10999.00, 3, 'urun_696e28406bd3f.JPG', 'aktif', 198, NOW(), NOW()),

(23, 11, 1, 'DJI Mini 3 Pro Drone', 'DJI Mini 3 Pro Fly More Combo. 4K kamera, 34 dakika uçuş süresi.', 29999.00, 34999.00, 2, 'urun_697711b2d9bfa.JPG', 'aktif', 267, NOW(), NOW());

-- Auto increment ayarla
ALTER TABLE urunler AUTO_INCREMENT = 24;
