<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kategori_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategoriler')->cascadeOnDelete();
            $table->string('attribute_adi');
            $table->string('label');
            $table->enum('tip', ['text', 'number', 'select', 'multiselect'])->default('text');
            $table->json('secenekler')->nullable();
            $table->boolean('zorunlu')->default(false);
            $table->integer('sira')->default(0);
            $table->timestamps();

            $table->index(['kategori_id', 'sira']);
            $table->unique(['kategori_id', 'attribute_adi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategori_attributes');
    }
};
