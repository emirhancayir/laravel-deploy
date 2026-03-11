<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('baslik');
            $table->string('alt_baslik')->nullable();
            $table->string('resim')->nullable();
            $table->string('link')->nullable();
            $table->enum('tip', ['ozel', 'populer', 'yeni', 'indirimli'])->default('ozel');
            $table->integer('sira')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();

            $table->index(['aktif', 'sira']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
