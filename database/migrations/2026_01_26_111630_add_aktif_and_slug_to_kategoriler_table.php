<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kategoriler', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('kategori_adi');
            $table->boolean('aktif')->default(true)->after('komisyon_orani');
        });

        // Mevcut kategorilere slug ata
        $kategoriler = DB::table('kategoriler')->get();
        foreach ($kategoriler as $kategori) {
            DB::table('kategoriler')
                ->where('id', $kategori->id)
                ->update(['slug' => Str::slug($kategori->kategori_adi)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategoriler', function (Blueprint $table) {
            $table->dropColumn(['slug', 'aktif']);
        });
    }
};
