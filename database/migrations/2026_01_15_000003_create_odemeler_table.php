<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odemeler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alici_id')->constrained('users');
            $table->foreignId('satici_id')->constrained('users');
            $table->foreignId('urun_id')->constrained('urunler');
            $table->foreignId('teklif_id')->constrained('teklifler');
            $table->foreignId('konusma_id')->constrained('konusmalar');
            $table->foreignId('sepet_item_id')->nullable()->constrained('sepet_items')->nullOnDelete();

            // iyzico fields
            $table->string('conversation_id')->unique();
            $table->string('iyzico_payment_id')->nullable();
            $table->string('iyzico_token')->nullable();
            $table->string('payment_transaction_id')->nullable();

            // Amounts
            $table->decimal('urun_tutari', 10, 2);
            $table->decimal('kargo_tutari', 10, 2)->default(0);
            $table->decimal('komisyon_tutari', 10, 2)->default(0);
            $table->decimal('toplam_tutar', 10, 2);
            $table->decimal('satici_tutari', 10, 2);

            // Status
            $table->enum('durum', [
                'beklemede',
                'odendi',
                'onaylandi',
                'iptal',
                'iade',
                'basarisiz'
            ])->default('beklemede');

            $table->timestamp('odeme_tarihi')->nullable();
            $table->timestamp('onay_tarihi')->nullable();
            $table->text('hata_mesaji')->nullable();
            $table->json('iyzico_response')->nullable();

            // Shipping address
            $table->foreignId('teslimat_il_id')->nullable()->constrained('iller')->nullOnDelete();
            $table->foreignId('teslimat_ilce_id')->nullable()->constrained('ilceler')->nullOnDelete();
            $table->foreignId('teslimat_mahalle_id')->nullable()->constrained('mahalleler')->nullOnDelete();
            $table->text('teslimat_adres_detay')->nullable();
            $table->string('teslimat_telefon')->nullable();

            $table->timestamps();

            $table->index(['alici_id', 'durum']);
            $table->index(['satici_id', 'durum']);
            $table->index('iyzico_payment_id');
            $table->index('durum');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odemeler');
    }
};
