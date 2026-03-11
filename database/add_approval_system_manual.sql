-- Admin Approval System - Manual SQL

-- Add new columns to products table
ALTER TABLE urunler
ADD COLUMN onay_durumu ENUM('beklemede', 'onaylandi', 'reddedildi') NOT NULL DEFAULT 'beklemede' AFTER durum,
ADD COLUMN red_nedeni TEXT NULL AFTER onay_durumu,
ADD COLUMN onaylandi_tarih TIMESTAMP NULL AFTER red_nedeni;

-- Auto-approve existing products (if any)
UPDATE urunler SET onay_durumu = 'onaylandi' WHERE onay_durumu = 'beklemede';

-- Add migration record (to Laravel migrations table)
INSERT INTO migrations (migration, batch)
VALUES ('2026_01_28_000001_add_onay_durumu_to_urunler',
        (SELECT IFNULL(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) AS temp));
