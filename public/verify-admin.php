<?php
/**
 * Admin Doğrulama - Basit Script
 * Kullanımdan sonra SİLİN!
 */

$key = $_GET['key'] ?? '';
if ($key !== 'VERIFY2024') {
    die('Unauthorized. Use: verify-admin.php?key=VERIFY2024');
}

// Veritabanı bilgileri (.env'den veya manuel)
$host = '127.0.0.1';
$dbname = 'emir1106_laravel_db';  // Veritabanı adını değiştir
$username = 'root';               // Kullanıcı adını değiştir
$password = '';                   // Şifreyi değiştir

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Admin kullanıcıları doğrula
    $stmt = $pdo->prepare("UPDATE users SET email_verified_at = NOW() WHERE kullanici_tipi IN ('admin', 'super_admin') AND email_verified_at IS NULL");
    $stmt->execute();
    $count = $stmt->rowCount();

    echo "<h1>✅ Başarılı!</h1>";
    echo "<p>$count admin kullanıcısı doğrulandı.</p>";
    echo "<p style='color:red;'><strong>Bu dosyayı şimdi silin!</strong></p>";

} catch (PDOException $e) {
    echo "<h1>❌ Hata</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p>Veritabanı bilgilerini kontrol edin.</p>";
}
