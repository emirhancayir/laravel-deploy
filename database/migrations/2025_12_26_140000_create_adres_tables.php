<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // İller tablosu
        Schema::create('iller', function (Blueprint $table) {
            $table->id();
            $table->string('il_adi', 50);
            $table->string('plaka_kodu', 2)->unique();
        });

        // İlçeler tablosu
        Schema::create('ilceler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('il_id')->constrained('iller')->onDelete('cascade');
            $table->string('ilce_adi', 50);
            $table->index('il_id');
        });

        // Mahalleler tablosu
        Schema::create('mahalleler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ilce_id')->constrained('ilceler')->onDelete('cascade');
            $table->string('mahalle_adi', 100);
            $table->string('posta_kodu', 5)->nullable();
            $table->index('ilce_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahalleler');
        Schema::dropIfExists('ilceler');
        Schema::dropIfExists('iller');
    }
};
