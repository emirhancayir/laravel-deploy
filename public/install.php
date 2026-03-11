<?php
/**
 * FLAVOR - Web Installer
 * Bu dosyayı kurulumdan sonra SİLİN!
 */

// Session başlat (CSRF bypass)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Güvenlik kontrolü - basit bir anahtar
$secret_key = 'FLAVOR2024INSTALL';

if (!isset($_GET['key']) || $_GET['key'] !== $secret_key) {
    die('Unauthorized. Use: install.php?key=' . $secret_key);
}

// CSRF korumasını devre dışı bırak
$_SERVER['REQUEST_METHOD'] = 'GET';

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Console kernel kullan (web değil)
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<!DOCTYPE html><html><head><title>FLAVOR Installer</title>";
echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;background:#f5f5f5;}";
echo ".box{background:#fff;padding:20px;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);margin-bottom:20px;}";
echo ".success{color:#10b981;}.error{color:#ef4444;}.warning{color:#f59e0b;}";
echo "pre{background:#1a1a2e;color:#0f0;padding:15px;border-radius:5px;overflow-x:auto;}</style></head><body>";

echo "<div class='box'><h1>🚀 FLAVOR Installer</h1></div>";

// 1. APP_KEY kontrolü
echo "<div class='box'><h3>1. APP_KEY Kontrolü</h3>";
if (empty(env('APP_KEY'))) {
    echo "<p class='warning'>APP_KEY bulunamadı, oluşturuluyor...</p><pre>";
    $kernel->call('key:generate', ['--force' => true]);
    echo "</pre><p class='success'>✓ APP_KEY oluşturuldu!</p>";
} else {
    echo "<p class='success'>✓ APP_KEY mevcut</p>";
}
echo "</div>";

// 2. Migration
echo "<div class='box'><h3>2. Veritabanı Migration</h3><pre>";
try {
    $kernel->call('migrate', ['--force' => true]);
    echo "</pre><p class='success'>✓ Migration tamamlandı!</p>";
} catch (Exception $e) {
    echo "</pre><p class='error'>✗ Hata: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 3. Seeding
echo "<div class='box'><h3>3. Varsayılan Veriler (Seeding)</h3><pre>";
try {
    $kernel->call('db:seed', ['--force' => true]);
    echo "</pre><p class='success'>✓ Seeding tamamlandı!</p>";
} catch (Exception $e) {
    echo "</pre><p class='error'>✗ Hata: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 4. Storage Link
echo "<div class='box'><h3>4. Storage Link</h3><pre>";
try {
    $kernel->call('storage:link');
    echo "</pre><p class='success'>✓ Storage link oluşturuldu!</p>";
} catch (Exception $e) {
    echo "</pre><p class='warning'>⚠ " . $e->getMessage() . "</p>";
}
echo "</div>";

// 5. Admin kullanıcıları doğrula
echo "<div class='box'><h3>5. Admin Doğrulama</h3><pre>";
try {
    $updated = \DB::table('users')
        ->whereIn('kullanici_tipi', ['admin', 'super_admin'])
        ->whereNull('email_verified_at')
        ->update(['email_verified_at' => now()]);
    echo "Updated: $updated admin users";
    echo "</pre><p class='success'>✓ Admin kullanıcıları doğrulandı!</p>";
} catch (Exception $e) {
    echo "</pre><p class='warning'>⚠ " . $e->getMessage() . "</p>";
}
echo "</div>";

// 6. Cache temizleme
echo "<div class='box'><h3>6. Cache Temizleme</h3><pre>";
$kernel->call('config:clear');
$kernel->call('cache:clear');
$kernel->call('view:clear');
echo "</pre><p class='success'>✓ Cache temizlendi!</p></div>";

// Sonuç
echo "<div class='box' style='background:#10b981;color:#fff;'>";
echo "<h2>✓ Kurulum Tamamlandı!</h2>";
echo "<p><strong>Admin Girişi:</strong></p>";
echo "<ul>";
echo "<li>Email: <code>admin@admin.com</code></li>";
echo "<li>Şifre: <code>Xk9#mP2\$vL7@nQ4!</code></li>";
echo "</ul>";
echo "<p style='margin-top:20px;padding:15px;background:rgba(0,0,0,0.2);border-radius:5px;'>";
echo "⚠️ <strong>ÖNEMLİ:</strong> Bu dosyayı (install.php) hemen silin!</p>";
echo "</div>";

echo "</body></html>";
