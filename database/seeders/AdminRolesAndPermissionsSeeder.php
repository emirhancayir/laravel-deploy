<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminRolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Varsayilan roller
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Tum yetkilere sahip en ust duzey yonetici',
                'is_system' => true,
            ],
            [
                'name' => 'admin',
                'display_name' => 'Admin',
                'description' => 'Genel yonetici',
                'is_system' => true,
            ],
            [
                'name' => 'moderator',
                'display_name' => 'Moderator',
                'description' => 'Icerik moderasyonu yapabilir',
                'is_system' => false,
            ],
        ];

        foreach ($roles as $role) {
            DB::table('admin_roles')->updateOrInsert(
                ['name' => $role['name']],
                array_merge($role, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // Varsayilan yetkiler
        $permissions = [
            // Kullanici yonetimi
            ['name' => 'users.view', 'display_name' => 'Kullanicilari Goruntule', 'group' => 'users'],
            ['name' => 'users.edit', 'display_name' => 'Kullanicilari Duzenle', 'group' => 'users'],
            ['name' => 'users.ban', 'display_name' => 'Kullanici Banla', 'group' => 'users'],
            ['name' => 'users.delete', 'display_name' => 'Kullanici Sil', 'group' => 'users'],
            ['name' => 'users.roles', 'display_name' => 'Rol Atama', 'group' => 'users'],

            // Urun yonetimi
            ['name' => 'products.view', 'display_name' => 'Urunleri Goruntule', 'group' => 'products'],
            ['name' => 'products.edit', 'display_name' => 'Urunleri Duzenle', 'group' => 'products'],
            ['name' => 'products.delete', 'display_name' => 'Urun Sil', 'group' => 'products'],
            ['name' => 'products.approve', 'display_name' => 'Urun Onayla/Reddet', 'group' => 'products'],

            // Kategori yonetimi
            ['name' => 'categories.view', 'display_name' => 'Kategorileri Goruntule', 'group' => 'categories'],
            ['name' => 'categories.create', 'display_name' => 'Kategori Olustur', 'group' => 'categories'],
            ['name' => 'categories.edit', 'display_name' => 'Kategori Duzenle', 'group' => 'categories'],
            ['name' => 'categories.delete', 'display_name' => 'Kategori Sil', 'group' => 'categories'],

            // Siparis yonetimi
            ['name' => 'orders.view', 'display_name' => 'Siparisleri Goruntule', 'group' => 'orders'],
            ['name' => 'orders.edit', 'display_name' => 'Siparis Duzenle', 'group' => 'orders'],
            ['name' => 'orders.refund', 'display_name' => 'Iade Islemleri', 'group' => 'orders'],

            // Teklif yonetimi
            ['name' => 'offers.view', 'display_name' => 'Teklifleri Goruntule', 'group' => 'offers'],
            ['name' => 'offers.manage', 'display_name' => 'Teklif Yonetimi', 'group' => 'offers'],

            // Site ayarlari
            ['name' => 'settings.view', 'display_name' => 'Ayarlari Goruntule', 'group' => 'settings'],
            ['name' => 'settings.edit', 'display_name' => 'Ayarlari Duzenle', 'group' => 'settings'],

            // Raporlar
            ['name' => 'reports.view', 'display_name' => 'Raporlari Goruntule', 'group' => 'reports'],
            ['name' => 'reports.export', 'display_name' => 'Rapor Disari Aktar', 'group' => 'reports'],

            // IP yonetimi
            ['name' => 'ip.view', 'display_name' => 'IP Kayitlarini Goruntule', 'group' => 'ip'],
            ['name' => 'ip.ban', 'display_name' => 'IP Banla', 'group' => 'ip'],

            // Rol yonetimi
            ['name' => 'roles.view', 'display_name' => 'Rolleri Goruntule', 'group' => 'roles'],
            ['name' => 'roles.create', 'display_name' => 'Rol Olustur', 'group' => 'roles'],
            ['name' => 'roles.edit', 'display_name' => 'Rol Duzenle', 'group' => 'roles'],
            ['name' => 'roles.delete', 'display_name' => 'Rol Sil', 'group' => 'roles'],

            // Activity log
            ['name' => 'logs.view', 'display_name' => 'Aktivite Loglarini Goruntule', 'group' => 'logs'],
        ];

        foreach ($permissions as $permission) {
            DB::table('admin_permissions')->updateOrInsert(
                ['name' => $permission['name']],
                array_merge($permission, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // Super admin'e tum yetkileri ver
        $superAdminRole = DB::table('admin_roles')->where('name', 'super_admin')->first();
        $allPermissions = DB::table('admin_permissions')->pluck('id');

        foreach ($allPermissions as $permissionId) {
            DB::table('role_permissions')->updateOrInsert([
                'role_id' => $superAdminRole->id,
                'permission_id' => $permissionId,
            ]);
        }

        // Admin rolune temel yetkileri ver
        $adminRole = DB::table('admin_roles')->where('name', 'admin')->first();
        $adminPermissions = DB::table('admin_permissions')
            ->whereIn('name', [
                'users.view', 'users.edit', 'users.ban',
                'products.view', 'products.edit', 'products.approve',
                'categories.view', 'categories.edit',
                'orders.view', 'orders.edit',
                'offers.view', 'offers.manage',
                'settings.view',
                'reports.view',
                'ip.view',
                'logs.view',
            ])
            ->pluck('id');

        foreach ($adminPermissions as $permissionId) {
            DB::table('role_permissions')->updateOrInsert([
                'role_id' => $adminRole->id,
                'permission_id' => $permissionId,
            ]);
        }

        // Moderator rolune sadece icerik yetkilerini ver
        $moderatorRole = DB::table('admin_roles')->where('name', 'moderator')->first();
        $moderatorPermissions = DB::table('admin_permissions')
            ->whereIn('name', [
                'users.view',
                'products.view', 'products.approve',
                'categories.view',
                'offers.view',
            ])
            ->pluck('id');

        foreach ($moderatorPermissions as $permissionId) {
            DB::table('role_permissions')->updateOrInsert([
                'role_id' => $moderatorRole->id,
                'permission_id' => $permissionId,
            ]);
        }
    }
}
