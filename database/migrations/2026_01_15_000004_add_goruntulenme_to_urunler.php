<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('urunler', function (Blueprint $table) {
            $table->unsignedInteger('goruntulenme_sayisi')->default(0)->after('satildi');
            $table->decimal('eski_fiyat', 10, 2)->nullable()->after('fiyat');
        });
    }

    public function down(): void
    {
        Schema::table('urunler', function (Blueprint $table) {
            $table->dropColumn(['goruntulenme_sayisi', 'eski_fiyat']);
        });
    }
};
