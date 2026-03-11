<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sepet_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kullanici_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('urun_id')->constrained('urunler')->cascadeOnDelete();
            $table->foreignId('teklif_id')->constrained('teklifler')->cascadeOnDelete();
            $table->foreignId('konusma_id')->constrained('konusmalar')->cascadeOnDelete();
            $table->decimal('fiyat', 10, 2);
            $table->timestamps();

            $table->unique(['kullanici_id', 'teklif_id']);
            $table->index(['kullanici_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sepet_items');
    }
};
