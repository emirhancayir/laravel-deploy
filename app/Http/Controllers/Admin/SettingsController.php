<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    /**
     * Ayarlar sayfasi
     */
    public function index()
    {
        $settings = SiteSetting::all();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Ayarlari kaydet
     */
    public function update(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            $setting = SiteSetting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value]);
            }
        }

        // Cache'i temizle
        SiteSetting::clearCache();

        return back()->with('basarili', 'Ayarlar kaydedildi.');
    }

    /**
     * Yeni ayar ekle
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:100|unique:site_settings,key',
            'value' => 'required|string',
            'label' => 'nullable|string|max:100',
            'type' => 'required|in:text,number,boolean,textarea',
            'description' => 'nullable|string|max:500',
        ]);

        SiteSetting::create([
            'key' => $request->key,
            'value' => $request->value,
            'label' => $request->label ?? $request->key,
            'type' => $request->type,
            'description' => $request->description,
            'group' => 'general',
        ]);

        // Cache'i temizle
        SiteSetting::clearCache();

        return back()->with('basarili', 'Ayar eklendi.');
    }

    /**
     * Ayar sil
     */
    public function destroy(SiteSetting $setting)
    {
        $key = $setting->key;
        $setting->delete();

        SiteSetting::clearCache();

        AdminActivityLog::log('settings.deleted', null, ['key' => $key], null, "Ayar silindi: {$key}");

        return back()->with('basarili', 'Ayar silindi.');
    }

    /**
     * Tüm cache'leri temizle
     */
    public function clearCache()
    {
        try {
            // Laravel cache'lerini temizle
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            // Site ayarları cache'ini temizle
            SiteSetting::clearCache();

            // Genel cache'i temizle
            Cache::flush();

            return back()->with('basarili', 'Tüm cache\'ler temizlendi.');
        } catch (\Exception $e) {
            return back()->with('hata', 'Cache temizlenirken hata: ' . $e->getMessage());
        }
    }
}
