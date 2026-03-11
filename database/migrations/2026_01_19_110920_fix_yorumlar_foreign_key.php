<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite'da foreign key değiştirmek için tabloyu yeniden oluşturmak gerekiyor
        // Önce verileri yedekle
        $yorumlar = DB::table('yorumlar')->get();

        // Tabloyu sil
        Schema::dropIfExists('yorumlar');

        // Tabloyu siparisler referansı olmadan yeniden oluştur
        Schema::create('yorumlar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kullanici_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('urun_id')->constrained('urunler')->onDelete('cascade');
            $table->unsignedBigInteger('siparis_id')->nullable(); // Foreign key olmadan
            $table->integer('puan');
            $table->text('yorum')->nullable();
            $table->boolean('onaylandi')->default(false);
            $table->datetime('onay_tarihi')->nullable();
            $table->timestamps();
        });

        // Verileri geri yükle
        foreach ($yorumlar as $yorum) {
            DB::table('yorumlar')->insert([
                'id' => $yorum->id,
                'kullanici_id' => $yorum->kullanici_id,
                'urun_id' => $yorum->urun_id,
                'siparis_id' => $yorum->siparis_id,
                'puan' => $yorum->puan,
                'yorum' => $yorum->yorum,
                'onaylandi' => $yorum->onaylandi,
                'onay_tarihi' => $yorum->onay_tarihi,
                'created_at' => $yorum->created_at,
                'updated_at' => $yorum->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri alma gerekirse aynı mantıkla yapılabilir
    }
};
