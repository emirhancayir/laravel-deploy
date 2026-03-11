<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesajlar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konusma_id')->constrained('konusmalar')->onDelete('cascade');
            $table->foreignId('gonderen_id')->constrained('users')->onDelete('cascade');
            $table->text('mesaj');
            $table->enum('tip', ['metin', 'resim', 'sistem', 'teklif'])->default('metin');
            $table->boolean('okundu')->default(false);
            $table->timestamp('okunma_tarihi')->nullable();
            $table->timestamps();

            // İndeksler
            $table->index(['konusma_id', 'created_at']);
            $table->index(['gonderen_id', 'okundu']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesajlar');
    }
};
