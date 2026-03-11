<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konusmalar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('urun_id')->constrained('urunler')->onDelete('cascade');
            $table->foreignId('alici_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('satici_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('son_mesaj_tarihi')->nullable();
            $table->integer('okunmamis_alici')->default(0);
            $table->integer('okunmamis_satici')->default(0);
            $table->enum('durum', ['aktif', 'arsivlendi', 'engellendi'])->default('aktif');
            $table->timestamps();

            // Her ürün için alıcı-satıcı çifti unique olmalı
            $table->unique(['urun_id', 'alici_id', 'satici_id']);

            // İndeksler
            $table->index(['alici_id', 'son_mesaj_tarihi']);
            $table->index(['satici_id', 'son_mesaj_tarihi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konusmalar');
    }
};
