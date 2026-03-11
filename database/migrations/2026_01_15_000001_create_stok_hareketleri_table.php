<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_hareketleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('urun_id')->constrained('urunler')->cascadeOnDelete();
            $table->foreignId('kullanici_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('hareket_tipi', ['giris', 'cikis', 'duzeltme', 'satis', 'iptal']);
            $table->integer('miktar');
            $table->integer('onceki_stok');
            $table->integer('sonraki_stok');
            $table->string('aciklama')->nullable();
            $table->timestamps();

            $table->index(['urun_id', 'created_at']);
            $table->index('hareket_tipi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_hareketleri');
    }
};
