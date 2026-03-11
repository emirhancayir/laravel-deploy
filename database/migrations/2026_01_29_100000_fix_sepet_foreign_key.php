<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Orphan kayıtları sil
        DB::statement('DELETE FROM sepet WHERE urun_id NOT IN (SELECT id FROM urunler)');

        // Mevcut foreign key varsa kaldır
        Schema::table('sepet', function (Blueprint $table) {
            // Önce index'i kaldırmayı dene
            try {
                $table->dropForeign(['urun_id']);
            } catch (\Exception $e) {
                // Zaten yoksa devam et
            }
        });

        // Sütun tipini düzelt ve foreign key ekle
        Schema::table('sepet', function (Blueprint $table) {
            $table->unsignedBigInteger('urun_id')->change();
            $table->foreign('urun_id')->references('id')->on('urunler')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('sepet', function (Blueprint $table) {
            $table->dropForeign(['urun_id']);
        });
    }
};
