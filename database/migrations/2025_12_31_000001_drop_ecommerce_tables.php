<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Önce foreign key bağımlılıkları olan tabloları sil
        Schema::dropIfExists('siparis_detaylari');
        Schema::dropIfExists('siparisler');
        Schema::dropIfExists('sepet');
    }

    public function down(): void
    {
        // Sepet tablosu
        Schema::create('sepet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kullanici_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->foreignId('urun_id')->constrained('urunler')->onDelete('cascade');
            $table->integer('miktar')->default(1);
            $table->timestamps();
        });

        // Siparisler tablosu
        Schema::create('siparisler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kullanici_id')->constrained('users')->onDelete('cascade');
            $table->decimal('toplam_tutar', 10, 2);
            $table->enum('durum', ['beklemede', 'onaylandi', 'kargoda', 'teslim_edildi', 'iptal'])->default('beklemede');
            $table->text('adres');
            $table->timestamps();
        });

        // Siparis detayları tablosu
        Schema::create('siparis_detaylari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siparis_id')->constrained('siparisler')->onDelete('cascade');
            $table->foreignId('urun_id')->constrained('urunler')->onDelete('cascade');
            $table->integer('miktar');
            $table->decimal('birim_fiyat', 10, 2);
            $table->timestamps();
        });
    }
};
