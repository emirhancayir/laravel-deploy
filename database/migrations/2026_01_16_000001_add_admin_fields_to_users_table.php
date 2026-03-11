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
        // Mevcut enum'a admin ve super_admin ekle (sadece MySQL icin)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN kullanici_tipi ENUM('alici', 'satici', 'admin', 'super_admin') DEFAULT 'alici'");
        }

        Schema::table('users', function (Blueprint $table) {
            // Ban alanlari
            $table->boolean('is_banned')->default(false)->after('kullanici_tipi');
            $table->timestamp('banned_at')->nullable()->after('is_banned');
            $table->string('ban_reason')->nullable()->after('banned_at');
            $table->foreignId('banned_by')->nullable()->after('ban_reason')->constrained('users')->nullOnDelete();

            // IP takip alanlari
            $table->string('registration_ip', 45)->nullable()->after('banned_by');
            $table->string('last_login_ip', 45)->nullable()->after('registration_ip');
            $table->timestamp('last_login_at')->nullable()->after('last_login_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['banned_by']);
            $table->dropColumn([
                'is_banned',
                'banned_at',
                'ban_reason',
                'banned_by',
                'registration_ip',
                'last_login_ip',
                'last_login_at'
            ]);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN kullanici_tipi ENUM('alici', 'satici') DEFAULT 'alici'");
        }
    }
};
