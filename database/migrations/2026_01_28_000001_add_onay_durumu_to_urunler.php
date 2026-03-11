<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('urunler', function (Blueprint $table) {
            $table->enum('onay_durumu', ['beklemede', 'onaylandi', 'reddedildi'])
                  ->default('beklemede')
                  ->after('durum');
            $table->text('red_nedeni')->nullable()->after('onay_durumu');
            $table->timestamp('onaylandi_tarih')->nullable()->after('red_nedeni');
        });
    }

    public function down(): void
    {
        Schema::table('urunler', function (Blueprint $table) {
            $table->dropColumn(['onay_durumu', 'red_nedeni', 'onaylandi_tarih']);
        });
    }
};
