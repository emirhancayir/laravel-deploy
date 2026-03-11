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
        Schema::table('kategoriler', function (Blueprint $table) {
            $table->decimal('komisyon_orani', 5, 2)->default(5.00)->after('aktif');
        });

        // Varsayilan komisyon oranlarini ayarla
        $oranlar = [
            'Elektronik' => 8.00,
            'Giyim' => 12.00,
            'Ev & Yaşam' => 10.00,
            'Spor & Açık Hava' => 10.00,
            'Kitap & Kırtasiye' => 15.00,
            'Kozmetik' => 12.00,
            'Vasıta' => 5.00,
        ];

        foreach ($oranlar as $kategoriAdi => $oran) {
            DB::table('kategoriler')
                ->where('kategori_adi', $kategoriAdi)
                ->update(['komisyon_orani' => $oran]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategoriler', function (Blueprint $table) {
            $table->dropColumn('komisyon_orani');
        });
    }
};
