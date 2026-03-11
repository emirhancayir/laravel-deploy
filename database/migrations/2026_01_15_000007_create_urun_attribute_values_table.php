<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('urun_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('urun_id')->constrained('urunler')->cascadeOnDelete();
            $table->foreignId('attribute_id')->constrained('kategori_attributes')->cascadeOnDelete();
            $table->text('deger');
            $table->timestamps();

            $table->unique(['urun_id', 'attribute_id']);
            $table->index('urun_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('urun_attribute_values');
    }
};
