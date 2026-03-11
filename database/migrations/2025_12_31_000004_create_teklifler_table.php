<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teklifler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konusma_id')->constrained('konusmalar')->onDelete('cascade');
            $table->foreignId('mesaj_id')->nullable()->constrained('mesajlar')->onDelete('set null');
            $table->foreignId('teklif_eden_id')->constrained('users')->onDelete('cascade');
            $table->decimal('tutar', 10, 2);
            $table->enum('durum', ['beklemede', 'kabul_edildi', 'reddedildi', 'iptal', 'suresi_doldu'])->default('beklemede');
            $table->timestamp('cevap_tarihi')->nullable();
            $table->timestamp('gecerlilik_tarihi')->nullable();
            $table->text('not')->nullable();
            $table->timestamps();

            // İndeksler
            $table->index(['konusma_id', 'durum']);
            $table->index(['teklif_eden_id', 'durum']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teklifler');
    }
};
