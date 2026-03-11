<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Yönetim Paneli') - ZAMASON</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23ff9900'/><circle cx='50' cy='38' r='16' fill='white'/><ellipse cx='50' cy='80' rx='28' ry='20' fill='white'/></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dark-mode-fixes.css') }}?v={{ time() }}">
    <style>
        :root {
            --primary: #ff9900;
            --secondary: #e68a00;
            --sidebar-bg: #1a1c23;
            --sidebar-hover: #2d303a;
        }
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: var(--sidebar-bg);
            color: #fff;
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar-header {
            padding: 20px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            text-align: center;
        }
        .sidebar-header h4 {
            margin: 0;
            font-weight: 700;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #a0aec0;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: var(--sidebar-hover);
            color: #fff;
            border-left-color: var(--primary);
        }
        .sidebar-menu li a i {
            width: 25px;
            margin-right: 10px;
        }
        .main-content {
            margin-left: 260px;
            padding: 20px;
            min-height: 100vh;
        }
        .top-bar {
            background: #fff;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stat-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #fff;
        }
        .stat-card.blue .icon { background: linear-gradient(135deg, #ff9900, #e68a00); }
        .stat-card.green .icon { background: linear-gradient(135deg, #11998e, #38ef7d); }
        .stat-card.orange .icon { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-card.red .icon { background: linear-gradient(135deg, #eb3349, #f45c43); }
        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
        }
        .stat-card .label {
            color: #718096;
            font-size: 14px;
        }
        .card-custom {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: none;
        }
        .card-custom .card-header {
            background: transparent;
            border-bottom: 1px solid #edf2f7;
            padding: 20px;
            font-weight: 600;
        }
        .table-custom th {
            background: #f7fafc;
            font-weight: 600;
            color: #4a5568;
            border: none;
        }
        .table-custom td {
            vertical-align: middle;
            border-color: #edf2f7;
        }
        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .btn-action {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
        }
        /* Quick Access Cards */
        .quick-access-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px 10px;
            background: #f8f9fa;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-align: center;
            height: 100%;
        }
        .quick-access-card:hover {
            background: #e9ecef;
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .quick-access-card .qa-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
            margin-bottom: 10px;
        }
        .quick-access-card .qa-label {
            font-size: 13px;
            font-weight: 600;
            color: #4a5568;
        }
        .quick-access-card-sm {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: #4a5568;
            font-size: 14px;
            font-weight: 500;
        }
        .quick-access-card-sm:hover {
            background: #e2e8f0;
            color: #2d3748;
            transform: translateX(5px);
        }
        .quick-access-card-sm i {
            color: #ff9900;
        }
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-shield-alt me-2"></i>ZAMASON</h4>
            <small>Yönetim Paneli</small>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Kontrol Paneli
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Kullanıcılar
                </a>
            </li>
            <li>
                <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.index') || request()->routeIs('admin.products.show') || request()->routeIs('admin.products.edit') ? 'active' : '' }}">
                    <i class="fas fa-box"></i> Tüm Ürünler
                </a>
            </li>
            <li>
                <a href="{{ route('admin.products.pending') }}" class="{{ request()->routeIs('admin.products.pending') ? 'active' : '' }}">
                    <i class="fas fa-clock"></i> Onay Bekleyen
                    @php
                        $bekleyenUrun = \App\Models\Urun::where('onay_durumu', 'beklemede')->count();
                    @endphp
                    @if($bekleyenUrun > 0)
                        <span class="badge bg-warning ms-auto">{{ $bekleyenUrun }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ route('admin.lists.index') }}" class="{{ request()->routeIs('admin.lists.*') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i> Listeler
                </a>
            </li>
            <li>
                <a href="{{ route('admin.ip.index') }}" class="{{ request()->routeIs('admin.ip.*') ? 'active' : '' }}">
                    <i class="fas fa-network-wired"></i> IP Yönetimi
                </a>
            </li>
            <li>
                <a href="{{ route('admin.sliders.index') }}" class="{{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}">
                    <i class="fas fa-images"></i> Slayt Yönetimi
                </a>
            </li>
            <li>
                <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fas fa-tags"></i> Kategori Yönetimi
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="fas fa-comments"></i> Yorumlar
                    @php
                        $bekleyenYorum = \App\Models\Yorum::bekleyen()->count();
                    @endphp
                    @if($bekleyenYorum > 0)
                        <span class="badge bg-warning ms-auto">{{ $bekleyenYorum }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Site Ayarları
                </a>
            </li>
            <li>
                <a href="{{ route('admin.activities.index') }}" class="{{ request()->routeIs('admin.activities.*') ? 'active' : '' }}">
                    <i class="fas fa-history"></i> Aktivite Logları
                </a>
            </li>
            <li class="mt-4 pt-4" style="border-top: 1px solid #2d303a;">
                <a href="{{ route('home') }}">
                    <i class="fas fa-home"></i> Siteye Dön
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <h5 class="mb-0">@yield('page-title', 'Kontrol Paneli')</h5>
            <div class="d-flex align-items-center">
                <span class="me-3 text-muted">{{ auth()->user()->ad }} {{ auth()->user()->soyad }}</span>
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                    {{ strtoupper(substr(auth()->user()->ad, 0, 1)) }}
                </div>
            </div>
        </div>

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')

    <!-- Toast Container -->
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;"></div>

    <script>
    // Toast Notification System
    function showToast(options) {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        const colors = {
            success: { bg: '#10b981', icon: 'fa-check-circle' },
            warning: { bg: '#f59e0b', icon: 'fa-exclamation-triangle' },
            danger: { bg: '#ef4444', icon: 'fa-times-circle' },
            info: { bg: '#3b82f6', icon: 'fa-info-circle' }
        };
        const config = colors[options.type] || colors.info;

        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px; background: #fff; padding: 16px 20px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); border-left: 4px solid ${config.bg}; min-width: 300px; max-width: 400px; animation: slideIn 0.3s ease;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: ${config.bg}15; display: flex; align-items: center; justify-content: center;">
                    <i class="fas ${options.icon || config.icon}" style="color: ${config.bg}; font-size: 18px;"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 600; color: #333; margin-bottom: 2px;">${options.title}</div>
                    <div style="font-size: 13px; color: #666;">${options.message}</div>
                </div>
                <button onclick="this.closest('div').parentElement.remove()" style="background: none; border: none; color: #999; cursor: pointer; padding: 4px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        if (options.url) {
            toast.style.cursor = 'pointer';
            toast.onclick = (e) => {
                if (!e.target.closest('button')) {
                    window.location.href = options.url;
                }
            };
        }

        container.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    // Session flash messages as toast
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('basarili'))
            showToast({ type: 'success', title: 'Başarılı', message: {!! json_encode(session('basarili')) !!} });
        @endif
        @if(session('başarılı'))
            showToast({ type: 'success', title: 'Başarılı', message: {!! json_encode(session('başarılı')) !!} });
        @endif
        @if(session('hata'))
            showToast({ type: 'danger', title: 'Hata', message: {!! json_encode(session('hata')) !!} });
        @endif
        @if(session('uyari'))
            showToast({ type: 'warning', title: 'Uyarı', message: {!! json_encode(session('uyari')) !!} });
        @endif
        @if(session('bilgi'))
            showToast({ type: 'info', title: 'Bilgi', message: {!! json_encode(session('bilgi')) !!} });
        @endif
        @if(session('success'))
            showToast({ type: 'success', title: 'Başarılı', message: {!! json_encode(session('success')) !!} });
        @endif
        @if(session('error'))
            showToast({ type: 'danger', title: 'Hata', message: {!! json_encode(session('error')) !!} });
        @endif
    });

    // Polling for notifications
    let lastCheck = Date.now();
    async function checkNotifications() {
        try {
            const response = await fetch('{{ route("admin.notifications") }}');
            const data = await response.json();

            // Show new notifications
            if (data.notifications && data.notifications.length > 0) {
                data.notifications.forEach(n => showToast(n));
            }

            // Update sidebar badges
            const pendingBadge = document.querySelector('.sidebar-menu .badge.bg-warning');
            if (pendingBadge && data.counts) {
                pendingBadge.textContent = data.counts.pending_products;
                pendingBadge.style.display = data.counts.pending_products > 0 ? 'inline' : 'none';
            }
        } catch (e) {
            console.log('Notification check failed:', e);
        }
    }

    // Check every 30 seconds
    setInterval(checkNotifications, 30000);
    // Initial check after 2 seconds (to set baseline)
    setTimeout(checkNotifications, 2000);

    // CSS Animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    (function() {
        const modalHtml = `
            <div id="confirm-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
                <div id="confirm-modal" style="background: #fff; border-radius: 16px; padding: 24px; max-width: 400px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
                    <div id="confirm-modal-icon" style="width: 60px; height: 60px; border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; font-size: 24px;"></div>
                    <h3 id="confirm-modal-title" style="text-align: center; color: #333; margin-bottom: 12px; font-size: 1.2rem;"></h3>
                    <p id="confirm-modal-message" style="text-align: center; color: #666; margin-bottom: 24px; font-size: 0.95rem;"></p>
                    <div style="display: flex; gap: 12px; justify-content: center;">
                        <button id="confirm-modal-cancel" style="padding: 10px 24px; border-radius: 8px; border: 1px solid #ddd; background: #f5f5f5; color: #333; font-size: 0.95rem; cursor: pointer;">İptal</button>
                        <button id="confirm-modal-ok" style="padding: 10px 24px; border-radius: 8px; border: none; font-size: 0.95rem; cursor: pointer; color: #fff;"></button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        const overlay = document.getElementById('confirm-modal-overlay');
        const iconEl = document.getElementById('confirm-modal-icon');
        const titleEl = document.getElementById('confirm-modal-title');
        const messageEl = document.getElementById('confirm-modal-message');
        const cancelBtn = document.getElementById('confirm-modal-cancel');
        const okBtn = document.getElementById('confirm-modal-ok');
        let resolvePromise = null;

        window.showConfirm = function(options) {
            return new Promise((resolve) => {
                resolvePromise = resolve;
                const type = options.type || 'warning';
                const title = options.title || 'Onay';
                const message = options.message || 'Bu işlemi gerçekleştirmek istediğinizden emin misiniz?';
                const configs = {
                    warning: { icon: 'fa-exclamation-triangle', color: '#f59e0b', bg: '#fef3c7' },
                    danger: { icon: 'fa-trash-alt', color: '#ef4444', bg: '#fee2e2' },
                    info: { icon: 'fa-info-circle', color: '#3b82f6', bg: '#dbeafe' },
                    success: { icon: 'fa-check-circle', color: '#10b981', bg: '#d1fae5' }
                };
                const config = configs[type] || configs.warning;
                iconEl.innerHTML = '<i class="fas ' + config.icon + '"></i>';
                iconEl.style.background = config.bg;
                iconEl.style.color = config.color;
                titleEl.textContent = title;
                messageEl.textContent = message;
                cancelBtn.textContent = options.cancelText || 'İptal';
                okBtn.textContent = options.confirmText || 'Evet';
                okBtn.style.background = config.color;
                overlay.style.display = 'flex';
                okBtn.focus();
            });
        };

        cancelBtn.onclick = function() { overlay.style.display = 'none'; if (resolvePromise) resolvePromise(false); };
        okBtn.onclick = function() { overlay.style.display = 'none'; if (resolvePromise) resolvePromise(true); };
        overlay.onclick = function(e) { if (e.target === overlay) { overlay.style.display = 'none'; if (resolvePromise) resolvePromise(false); } };
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && overlay.style.display === 'flex') { overlay.style.display = 'none'; if (resolvePromise) resolvePromise(false); } });
    })();
    </script>
</body>
</html>
