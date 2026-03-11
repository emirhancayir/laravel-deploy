<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // IP Listesi (blacklist/whitelist)
        Schema::create('ip_listeleri', function (Blueprint $table) {
            $table->id();
            $table->string('ip_adresi', 45); // IPv6 desteği
            $table->enum('tip', ['blacklist', 'whitelist'])->default('blacklist');
            $table->string('sebep')->nullable();
            $table->timestamp('bitis_tarihi')->nullable(); // null = kalıcı
            $table->foreignId('ekleyen_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index(['ip_adresi', 'tip', 'aktif']);
        });

        // Kullanıcı Engelleme (user-to-user)
        Schema::create('kullanici_engelleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('engelleyen_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('engellenen_id')->constrained('users')->cascadeOnDelete();
            $table->string('sebep')->nullable();
            $table->timestamps();

            $table->unique(['engelleyen_id', 'engellenen_id']);
            $table->index('engellenen_id');
        });

        // Yasaklı Kelimeler
        Schema::create('yasakli_kelimeler', function (Blueprint $table) {
            $table->id();
            $table->string('kelime');
            $table->enum('tip', ['tam_eslesme', 'icerir'])->default('icerir');
            $table->json('uygulanacak_alanlar'); // ['urun_adi', 'urun_aciklama', 'mesaj', 'kullanici_adi']
            $table->string('yerine')->nullable(); // Sansürleme için alternatif
            $table->enum('aksiyon', ['engelle', 'sansurle', 'uyar'])->default('engelle');
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index(['kelime', 'aktif']);
        });

        // E-posta Domain Listesi
        Schema::create('email_domain_listeleri', function (Blueprint $table) {
            $table->id();
            $table->string('domain'); // örn: tempmail.com
            $table->enum('tip', ['blacklist', 'whitelist'])->default('blacklist');
            $table->string('sebep')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->unique(['domain', 'tip']);
            $table->index(['domain', 'aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_domain_listeleri');
        Schema::dropIfExists('yasakli_kelimeler');
        Schema::dropIfExists('kullanici_engelleri');
        Schema::dropIfExists('ip_listeleri');
    }
};
