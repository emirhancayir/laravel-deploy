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
        // Admin rolleri
        Schema::create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('display_name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false); // Sistem rolleri silinemez
            $table->timestamps();
        });

        // Admin yetkileri
        Schema::create('admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique(); // Ornegin: users.view, users.edit
            $table->string('display_name', 100);
            $table->string('group', 50); // Gruplama icin: users, products, orders
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Rol-Yetki pivot tablosu
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('admin_roles')->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained('admin_permissions')->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        // Kullanici-Rol pivot tablosu
        Schema::create('user_roles', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('admin_roles')->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->primary(['user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('admin_permissions');
        Schema::dropIfExists('admin_roles');
    }
};
