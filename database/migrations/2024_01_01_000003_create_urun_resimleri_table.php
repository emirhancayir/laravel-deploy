<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('urun_resimleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('urun_id')->constrained('urunler')->onDelete('cascade');
            $table->string('resim');
            $table->integer('sira')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('urun_resimleri');
    }
};
