<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('urunler', function (Blueprint $table) {
            $table->foreignId('il_id')->nullable()->after('durum')->constrained('iller')->nullOnDelete();
            $table->foreignId('ilce_id')->nullable()->after('il_id')->constrained('ilceler')->nullOnDelete();
            $table->foreignId('mahalle_id')->nullable()->after('ilce_id')->constrained('mahalleler')->nullOnDelete();
            $table->string('adres_detay', 500)->nullable()->after('mahalle_id');
        });
    }

    public function down(): void
    {
        Schema::table('urunler', function (Blueprint $table) {
            $table->dropForeign(['il_id']);
            $table->dropForeign(['ilce_id']);
            $table->dropForeign(['mahalle_id']);
            $table->dropColumn(['il_id', 'ilce_id', 'mahalle_id', 'adres_detay']);
        });
    }
};
