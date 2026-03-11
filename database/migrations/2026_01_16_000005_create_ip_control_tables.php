<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // IP log tablosu - tum IP islemlerini takip
        Schema::create('ip_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->enum('action', ['registration', 'login', 'offer', 'other']);
            $table->text('user_agent')->nullable();
            $table->json('extra_data')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('ip_address');
            $table->index(['ip_address', 'action', 'created_at']);
        });

        // IP ban tablosu
        Schema::create('ip_bans', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->unique();
            $table->text('reason')->nullable();
            $table->foreignId('banned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('banned_at')->useCurrent();
            $table->timestamp('expires_at')->nullable(); // null = kalici ban
            $table->timestamps();

            $table->index('ip_address');
        });

        // Teklif tablosuna IP alani ekle
        Schema::table('teklifler', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->after('not');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teklifler', function (Blueprint $table) {
            $table->dropColumn('ip_address');
        });

        Schema::dropIfExists('ip_bans');
        Schema::dropIfExists('ip_logs');
    }
};
