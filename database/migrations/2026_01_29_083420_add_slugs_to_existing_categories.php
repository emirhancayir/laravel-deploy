<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $kategoriler = DB::table('kategoriler')->get();

        foreach ($kategoriler as $kategori) {
            if (empty($kategori->slug)) {
                DB::table('kategoriler')
                    ->where('id', $kategori->id)
                    ->update(['slug' => Str::slug($kategori->kategori_adi)]);
            }
        }
    }

    public function down(): void
    {
        // Slug'ları geri almaya gerek yok
    }
};
