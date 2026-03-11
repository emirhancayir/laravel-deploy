<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AdminRole;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    /**
     * Kullanici listesi
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Arama filtresi
        if ($request->filled('ara')) {
            $arama = $request->ara;
            $query->where(function ($q) use ($arama) {
                $q->where('ad', 'like', "%{$arama}%")
                    ->orWhere('soyad', 'like', "%{$arama}%")
                    ->orWhere('email', 'like', "%{$arama}%")
                    ->orWhere('telefon', 'like', "%{$arama}%");
            });
        }

        // Tip filtresi
        if ($request->filled('tip')) {
            $query->where('kullanici_tipi', $request->tip);
        }

        // Durum filtresi
        if ($request->filled('durum')) {
            if ($request->durum === 'banli') {
                $query->where('is_banned', true);
            } elseif ($request->durum === 'aktif') {
                $query->where('is_banned', false);
            }
        }

        // Siralama
        $siralama = $request->get('siralama', 'created_at');
        $sira = $request->get('sira', 'desc');
        $query->orderBy($siralama, $sira);

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Yeni kullanici ekleme formu
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Yeni kullanici kaydet
     */
    public function store(Request $request)
    {
        // Super admin ise admin tipi de seçilebilir
        $allowedTypes = ['alici', 'satici'];
        if (auth()->user()->superAdminMi()) {
            $allowedTypes[] = 'admin';
        }

        $validated = $request->validate([
            'ad' => 'required|string|max:255',
            'soyad' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telefon' => 'nullable|string|max:20',
            'sifre' => 'required|string|min:6',
            'kullanici_tipi' => 'required|in:' . implode(',', $allowedTypes),
            // Satici alanlari
            'firma_adi' => 'required_if:kullanici_tipi,satici|nullable|string|max:255',
            'vergi_no' => 'nullable|string|max:50',
            'adres' => 'required_if:kullanici_tipi,satici|nullable|string|max:500',
            'iban' => 'required_if:kullanici_tipi,satici|nullable|string|max:30',
        ], [
            'ad.required' => 'Ad zorunludur.',
            'soyad.required' => 'Soyad zorunludur.',
            'email.required' => 'Email zorunludur.',
            'email.unique' => 'Bu email adresi zaten kayıtlı.',
            'sifre.required' => 'Şifre zorunludur.',
            'sifre.min' => 'Şifre en az 6 karakter olmalıdır.',
            'firma_adi.required_if' => 'Satıcı için firma adı zorunludur.',
            'adres.required_if' => 'Satıcı için adres zorunludur.',
            'iban.required_if' => 'Satıcı için IBAN zorunludur.',
        ]);

        $userData = [
            'ad' => $validated['ad'],
            'soyad' => $validated['soyad'],
            'email' => $validated['email'],
            'telefon' => $validated['telefon'] ?? null,
            'password' => $validated['sifre'],
            'kullanici_tipi' => $validated['kullanici_tipi'],
            'email_verified_at' => now(), // Admin eklediği için doğrulanmış say
        ];

        // Satici ise ek bilgileri ekle
        if ($validated['kullanici_tipi'] === 'satici') {
            $userData['firma_adi'] = $validated['firma_adi'];
            $userData['vergi_no'] = $validated['vergi_no'] ?? null;
            $userData['adres'] = $validated['adres'];
            $userData['iban'] = $validated['iban'];
            $userData['satici_onay_tarihi'] = now();
        }

        $user = User::create($userData);

        AdminActivityLog::log('user.created', $user, null, $userData, 'Kullanıcı oluşturuldu: ' . $user->ad_soyad);

        return redirect()->route('admin.users.show', $user)
            ->with('basarili', 'Kullanıcı başarıyla oluşturuldu.');
    }

    /**
     * Kullanici detay
     */
    public function show(User $user)
    {
        $user->load(['urunler', 'adminRoles', 'ipLogs' => function ($q) {
            $q->latest()->take(10);
        }]);

        // Aktivite loglari
        $aktiviteler = AdminActivityLog::where('model_type', User::class)
            ->where('model_id', $user->id)
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('admin.users.show', compact('user', 'aktiviteler'));
    }

    /**
     * Kullaniciyi banla
     */
    public function ban(Request $request, User $user)
    {
        // Kendini banlayamaz
        if ($user->id === auth()->id()) {
            return back()->with('hata', 'Kendinizi banlayamazsiniz.');
        }

        // Super admin banlanamazsa (opsiyonel guvenlik)
        if ($user->superAdminMi() && !auth()->user()->superAdminMi()) {
            return back()->with('hata', 'Super admin kullanicilarini banlayamazsiniz.');
        }

        $request->validate([
            'sebep' => 'nullable|string|max:500',
        ]);

        $user->banla($request->sebep);

        return back()->with('basarili', "{$user->ad_soyad} kullanicisi banlandi.");
    }

    /**
     * Ban kaldir
     */
    public function unban(User $user)
    {
        $user->banKaldir();

        return back()->with('basarili', "{$user->ad_soyad} kullanicisinin bani kaldirildi.");
    }

    /**
     * Kullanici tipini degistir
     */
    public function changeTip(Request $request, User $user)
    {
        $request->validate([
            'tip' => 'required|in:alici,satici,admin,super_admin',
        ]);

        // Sadece super admin, baskasini admin yapabilir
        if (in_array($request->tip, ['admin', 'super_admin']) && !auth()->user()->superAdminMi()) {
            return back()->with('hata', 'Sadece super adminler admin atayabilir.');
        }

        $eskiTip = $user->kullanici_tipi;
        $user->update(['kullanici_tipi' => $request->tip]);

        AdminActivityLog::log('user.type_changed', $user, ['tip' => $eskiTip], ['tip' => $request->tip]);

        return back()->with('basarili', "Kullanici tipi guncellendi.");
    }

    /**
     * Rol ata
     */
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:admin_roles,id',
        ]);

        // Zaten bu role sahip mi?
        if ($user->adminRoles()->where('admin_roles.id', $request->role_id)->exists()) {
            return back()->with('hata', 'Kullanici zaten bu role sahip.');
        }

        $user->adminRoles()->attach($request->role_id, [
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
        ]);

        $role = AdminRole::find($request->role_id);
        AdminActivityLog::log('user.role_assigned', $user, null, ['role' => $role->name]);

        return back()->with('basarili', "{$role->display_name} rolu atandi.");
    }

    /**
     * Rol kaldir
     */
    public function removeRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:admin_roles,id',
        ]);

        $role = AdminRole::find($request->role_id);
        $user->adminRoles()->detach($request->role_id);

        AdminActivityLog::log('user.role_removed', $user, ['role' => $role->name], null);

        return back()->with('basarili', "{$role->display_name} rolu kaldirildi.");
    }

    /**
     * Kullanici sil
     */
    public function destroy(User $user)
    {
        // Kendini silemez
        if ($user->id === auth()->id()) {
            return back()->with('hata', 'Kendinizi silemezsiniz.');
        }

        // Super admin silinemez
        if ($user->superAdminMi()) {
            return back()->with('hata', 'Super admin kullanicilari silinemez.');
        }

        $adSoyad = $user->ad_soyad;

        AdminActivityLog::log('user.deleted', null, [
            'id' => $user->id,
            'email' => $user->email,
            'ad_soyad' => $adSoyad,
        ], null, "Kullanici silindi: {$adSoyad}");

        try {
            // Foreign key kontrolünü kapat
            \DB::statement('SET FOREIGN_KEY_CHECKS=0');

            $userId = $user->id;

            // Kullanıcıya ait tüm ilişkili verileri sil (tablo varsa)
            $tablolar = [
                'siparis_detaylari' => ['siparis_id', 'IN', 'siparisler', 'kullanici_id'],
                'siparisler' => ['kullanici_id', '='],
                'sepet' => ['kullanici_id', '='],
                'sepet_items' => ['kullanici_id', '='],
                'favoriler' => ['kullanici_id', '='],
                'yorumlar' => ['kullanici_id', '='],
                'notifications' => ['notifiable_id', '='],
                'kargolar' => ['kullanici_id', '='],
            ];

            foreach ($tablolar as $tablo => $config) {
                try {
                    if (\Schema::hasTable($tablo)) {
                        if ($config[1] === 'IN' && isset($config[2])) {
                            \DB::table($tablo)->whereIn($config[0], function($q) use ($config, $userId) {
                                $q->select('id')->from($config[2])->where($config[3], $userId);
                            })->delete();
                        } else {
                            \DB::table($tablo)->where($config[0], $userId)->delete();
                        }
                    }
                } catch (\Exception $e) {
                    // Tablo yoksa veya hata olursa devam et
                }
            }

            // Mesajlar ve konuşmalar (çift kolon kontrolü)
            try {
                if (\Schema::hasTable('mesajlar')) {
                    \DB::table('mesajlar')->where('gonderen_id', $userId)->delete();
                    \DB::table('mesajlar')->where('alici_id', $userId)->delete();
                }
            } catch (\Exception $e) {}

            try {
                if (\Schema::hasTable('konusmalar')) {
                    \DB::table('konusmalar')->where('alici_id', $userId)->delete();
                    \DB::table('konusmalar')->where('satici_id', $userId)->delete();
                }
            } catch (\Exception $e) {}

            try {
                if (\Schema::hasTable('teklifler')) {
                    \DB::table('teklifler')->where('alici_id', $userId)->delete();
                    \DB::table('teklifler')->where('satici_id', $userId)->delete();
                }
            } catch (\Exception $e) {}

            // Eğer satıcıysa ürünlerini de sil
            try {
                if ($user->saticiMi() && \Schema::hasTable('urunler')) {
                    $urunIds = \DB::table('urunler')->where('satici_id', $userId)->pluck('id')->toArray();
                    if (count($urunIds) > 0) {
                        if (\Schema::hasTable('urun_resimleri')) {
                            \DB::table('urun_resimleri')->whereIn('urun_id', $urunIds)->delete();
                        }
                        if (\Schema::hasTable('urun_attribute_values')) {
                            \DB::table('urun_attribute_values')->whereIn('urun_id', $urunIds)->delete();
                        }
                        if (\Schema::hasTable('yorumlar')) {
                            \DB::table('yorumlar')->whereIn('urun_id', $urunIds)->delete();
                        }
                        if (\Schema::hasTable('favoriler')) {
                            \DB::table('favoriler')->whereIn('urun_id', $urunIds)->delete();
                        }
                        \DB::table('urunler')->where('satici_id', $userId)->delete();
                    }
                }
            } catch (\Exception $e) {}

            // Kullanıcıyı sil
            \DB::table('users')->where('id', $userId)->delete();

            // Foreign key kontrolünü aç
            \DB::statement('SET FOREIGN_KEY_CHECKS=1');

        } catch (\Exception $e) {
            \DB::statement('SET FOREIGN_KEY_CHECKS=1');
            \Log::error('Kullanici silme hatasi: ' . $e->getMessage());
            return back()->with('hata', 'Kullanici silinirken bir hata olustu: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index')->with('basarili', "{$adSoyad} kullanicisi silindi.");
    }
}
