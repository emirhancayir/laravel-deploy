<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kargo firmaları tablosu
        Schema::create('kargo_firmalari', function (Blueprint $table) {
            $table->id();
            $table->string('firma_adi', 50);
            $table->string('logo')->nullable();
            $table->string('takip_url')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // Kargolar tablosu
        Schema::create('kargolar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konusma_id')->constrained('konusmalar')->onDelete('cascade');
            $table->foreignId('teklif_id')->nullable()->constrained('teklifler')->nullOnDelete();
            $table->foreignId('gonderen_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('alici_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('urun_id')->constrained('urunler')->onDelete('cascade');
            $table->foreignId('kargo_firmasi_id')->nullable()->constrained('kargo_firmalari')->nullOnDelete();
            $table->string('takip_no', 50)->nullable();
            $table->enum('durum', ['beklemede', 'hazirlaniyor', 'kargoda', 'teslim_edildi', 'iptal'])->default('beklemede');
            $table->decimal('urun_fiyati', 10, 2);
            $table->decimal('kargo_ucreti', 10, 2)->default(0);

            // Alıcı adresi
            $table->unsignedInteger('alici_il_id')->nullable();
            $table->unsignedInteger('alici_ilce_id')->nullable();
            $table->text('alici_adres_detay')->nullable();
            $table->string('alici_telefon', 20)->nullable();

            $table->text('notlar')->nullable();
            $table->timestamps();

            $table->foreign('alici_il_id')->references('id')->on('iller')->nullOnDelete();
            $table->foreign('alici_ilce_id')->references('id')->on('ilceler')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kargolar');
        Schema::dropIfExists('kargo_firmalari');
    }
};
