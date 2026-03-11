<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('urunler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('satici_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kategori_id')->nullable()->constrained('kategoriler')->onDelete('set null');
            $table->string('urun_adi');
            $table->text('aciklama')->nullable();
            $table->decimal('fiyat', 10, 2);
            $table->integer('stok')->default(0);
            $table->string('resim')->nullable();
            $table->enum('durum', ['aktif', 'pasif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('urunler');
    }
};
