<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IpLog;
use App\Models\IpBan;
use App\Models\User;
use App\Models\Teklif;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IpManagementController extends Controller
{
    /**
     * IP yonetimi ana sayfasi
     */
    public function index(Request $request)
    {
        // Son IP loglari
        $logs = IpLog::with('user')
            ->latest('created_at')
            ->paginate(30);

        return view('admin.ip.index', compact('logs'));
    }

    /**
     * Belirli bir IP'nin detaylari
     */
    public function show(string $ip)
    {
        // Bu IP'den kayitli kullanicilar
        $kullanicilar = User::where('registration_ip', $ip)
            ->orWhere('last_login_ip', $ip)
            ->get();

        // Bu IP'den yapilan teklifler
        $teklifler = Teklif::where('ip_address', $ip)
            ->with(['konusma.urun', 'teklifEden'])
            ->latest()
            ->take(20)
            ->get();

        // Bu IP'nin log kayitlari
        $loglar = IpLog::forIp($ip)
            ->with('user')
            ->latest('created_at')
            ->paginate(20);

        // Ban durumu
        $ban = IpBan::where('ip_address', $ip)->first();

        return view('admin.ip.show', compact('ip', 'kullanicilar', 'teklifler', 'loglar', 'ban'));
    }

    /**
     * IP banla
     */
    public function ban(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip',
            'sebep' => 'nullable|string|max:500',
            'sure' => 'nullable|integer',
        ]);

        $expiresAt = null;
        if ($request->sure && $request->sure > 0) {
            $expiresAt = now()->addDays($request->sure);
        }

        IpBan::create([
            'ip_address' => $request->ip,
            'reason' => $request->sebep,
            'banned_by' => auth()->id(),
            'expires_at' => $expiresAt,
        ]);

        return back()->with('basarili', "IP adresi banlandi: {$request->ip}");
    }

    /**
     * IP banini kaldir
     */
    public function unban(string $ip)
    {
        IpBan::unban($ip);

        AdminActivityLog::log('ip.unbanned', null, null, ['ip' => $ip], "IP bani kaldirildi: {$ip}");

        return back()->with('basarili', "IP bani kaldirildi: {$ip}");
    }

    /**
     * Banli IP listesi
     */
    public function bans()
    {
        $bans = IpBan::with('bannedBy')
            ->latest('created_at')
            ->paginate(20);

        return view('admin.ip.bans', compact('bans'));
    }

    /**
     * IP log arama
     */
    public function search(Request $request)
    {
        $request->validate([
            'ip' => 'required|string|min:3',
        ]);

        $ip = $request->ip;

        $loglar = IpLog::where('ip_address', 'like', "%{$ip}%")
            ->with('user')
            ->latest('created_at')
            ->paginate(20);

        $kullanicilar = User::where('registration_ip', 'like', "%{$ip}%")
            ->orWhere('last_login_ip', 'like', "%{$ip}%")
            ->get();

        return view('admin.ip.search', compact('ip', 'loglar', 'kullanicilar'));
    }
}
