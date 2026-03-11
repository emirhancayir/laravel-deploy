<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favoriler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kullanici_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('urun_id')->constrained('urunler')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['kullanici_id', 'urun_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favoriler');
    }
};
