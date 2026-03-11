<!DOCTYPE html>
<html lang="tr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" data-protected="true">
    <meta name="description" content="@yield('meta_description', 'ZAMASON - Multi-vendor e-commerce marketplace. Secure shopping, fast shipping.')">
    <meta name="keywords" content="e-commerce, online shopping, marketplace, multi-vendor, secure shopping">
    <meta name="author" content="ZAMASON">
    <meta name="robots" content="index, follow">
    <title>@yield('title', config('app.name', 'ZAMASON'))</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23ff9900'/><circle cx='50' cy='38' r='16' fill='white'/><ellipse cx='50' cy='80' rx='28' ry='20' fill='white'/></svg>">
    <link rel="apple-touch-icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23ff9900'/><circle cx='50' cy='38' r='16' fill='white'/><ellipse cx='50' cy='80' rx='28' ry='20' fill='white'/></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/dark-mode-fixes.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
    <script>
        // Sayfa yüklenmeden önce tema kontrolü (flash önleme)
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);

            // Dark mode için inline style düzeltmeleri
            if (savedTheme === 'dark') {
                document.addEventListener('DOMContentLoaded', function() {
                    // CSS var() kullanımlarını düzelt
                    const style = document.createElement('style');
                    style.textContent = `
                        [data-theme="dark"] {
                            --text-primary: #e0e0e0;
                            --text-secondary: #a0a0a0;
                            --text-light: #a0a0a0;
                            --text-muted: #a0a0a0;
                            --bg-color: #0f0f1e;
                            --card-bg: #1e1e2e;
                            --border: #3a3a5a;
                            --border-color: #3a3a5a;
                        }
                    `;
                    document.head.appendChild(style);
                });
            }
        })();
    </script>
</head>
<body>
    <header class="header" style="height:75px;">
        <nav class="navbar navbar-expand-lg"  >
            <div class="container-fluid" style="max-width: 1400px; position: relative;">
                <!-- Navbar row: Logo | Search (desktop) | Hamburger (mobile) -->
                <div class="navbar-main-row">
                    <!-- Logo (left) -->
                    <a href="{{ route('home') }}" class="navbar-brand logo mb-0">
                        <i class="fas fa-store"></i>
                        <span>{{ config('app.name', 'ZAMASON') }}</span>
                    </a>

                    <!-- Search bar - Desktop only (center) -->
                    <div class="search-bar d-none d-lg-block" style="max-width: 500px; width: 100%;">
                        <form action="{{ route('products.index') }}" method="GET">
                            <input type="text" name="arama" placeholder="Ürün ara..." value="{{ request('arama') }}">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <!-- Mobile hamburger (right) - mobile only -->
                    <button class="navbar-toggler border-0 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Desktop nav placeholder (right) -->
                    <div class="d-none d-lg-block">
                        <!-- Nav items will float here -->
                    </div>
                </div>

                <!-- Mobile search bar (full width below) -->
                <div class="search-bar d-lg-none w-100 mt-3">
                    <form action="{{ route('products.index') }}" method="GET">
                        <input type="text" name="arama" placeholder="Ürün ara..." value="{{ request('arama') }}">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <!-- Nav items -->
                <div class="collapse navbar-collapse desktop-nav-absolute" id="navbarNav">
                    <ul class="navbar-nav align-items-center">
                        @auth
                            <li class="nav-item">
                                <a href="{{ route('notifications.index') }}" class="nav-link position-relative d-flex align-items-center">
                                    <i class="fas fa-bell"></i>
                                    <span class="nav-text ms-2">Bildirimler</span>
                                    @php $okunmamisBildirim = auth()->user()->unreadNotifications()->count(); @endphp
                                    @if($okunmamisBildirim > 0)
                                        <span class="notification-count">{{ $okunmamisBildirim > 99 ? '99+' : $okunmamisBildirim }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('chat.index') }}" class="nav-link position-relative d-flex align-items-center">
                                    <i class="fas fa-comments"></i>
                                    <span class="nav-text ms-2">Mesajlar</span>
                                    @php $okunmamisSayi = auth()->user()->okunmamisMesajSayisi(); @endphp
                                    @if($okunmamisSayi > 0)
                                        <span class="message-count">{{ $okunmamisSayi > 99 ? '99+' : $okunmamisSayi }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('cart.index') }}" class="nav-link position-relative d-flex align-items-center">
                                    <i class="fas fa-shopping-cart"></i>
                                    <span class="nav-text ms-2">Sepet</span>
                                    @php $sepetSayisi = auth()->user()->sepetSayisi(); @endphp
                                    @if($sepetSayisi > 0)
                                        <span class="cart-count">{{ $sepetSayisi > 99 ? '99+' : $sepetSayisi }}</span>
                                    @endif
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                    @if(auth()->user()->profil_resmi)
                                        <img src="{{ asset('serve-image.php?p=profil/' . auth()->user()->profil_resmi) }}" alt="" class="header-avatar me-2">
                                    @else
                                        <span class="header-avatar-text me-2">{{ strtoupper(substr(auth()->user()->ad, 0, 1)) }}</span>
                                    @endif
                                    <span>{{ auth()->user()->ad }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if(in_array(auth()->user()->kullanici_tipi, ['admin', 'super_admin']))
                                        <li><a class="dropdown-item text-primary fw-bold" href="{{ route('admin.dashboard') }}"><i class="fas fa-shield-alt"></i> Admin Paneli</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                    @endif
                                    @if(auth()->user()->saticiMi())
                                        <li><a class="dropdown-item" href="{{ route('seller.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Satıcı Paneli</a></li>
                                        <li><a class="dropdown-item" href="{{ route('products.create') }}"><i class="fas fa-plus"></i> Ürün Ekle</a></li>
                                    @else
                                        <li><a class="dropdown-item" href="{{ route('seller.become') }}"><i class="fas fa-store"></i> Satıcı Ol</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="fas fa-user-edit"></i> Profilim</a></li>
                                    <li><a class="dropdown-item" href="{{ route('favorites.index') }}"><i class="fas fa-heart"></i> Favorilerim</a></li>
                                    <li><a class="dropdown-item" href="{{ route('cart.index') }}"><i class="fas fa-shopping-cart"></i> Sepetim</a></li>
                                    <li><a class="dropdown-item" href="{{ route('payment.list') }}"><i class="fas fa-receipt"></i> Siparişlerim</a></li>
                                    <li><a class="dropdown-item" href="{{ route('shipping.index') }}"><i class="fas fa-truck"></i> Kargolarım</a></li>
                                    <li><a class="dropdown-item" href="{{ route('chat.index') }}"><i class="fas fa-comments"></i> Mesajlarim</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                            @csrf
                                            <a href="#" onclick="this.closest('form').submit(); return false;" class="dropdown-item text-danger">
                                                <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                                            </a>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ route('login') }}" class="btn btn-outline">Giriş Yap</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('register') }}" class="btn btn-primary ms-2">Kayıt Ol</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="main-content">
        @if(session('başarılı'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('başarılı') }}
            </div>
        @endif

        @if(session('hata'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('hata') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3><i class="fas fa-store"></i> {{ config('app.name', 'ZAMASON') }}</h3>
                <p>Güvenilir alışverişin adresi. En kaliteli ürünler, en uygun fiyatlarla.</p>
                <div class="social-links justify-content-center">
                    <a href="https://instagram.com/em1rhanl" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Hızlı Linkler</h4>
                <ul>
                    <li><a href="{{ route('home') }}">Ana Sayfa</a></li>
                    <li><a href="{{ route('products.index', ['kategori' => 1]) }}">Elektronik</a></li>
                    <li><a href="{{ route('products.index', ['kategori' => 2]) }}">Giyim</a></li>
                    <li><a href="{{ route('products.index', ['kategori' => 3]) }}">Ev & Yaşam</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Hesabım</h4>
                <ul>
                    @auth
                        <li><a href="{{ route('chat.index') }}">Mesajlarım</a></li>
                        <li><a href="{{ route('favorites.index') }}">Favorilerim</a></li>
                        <li><a href="{{ route('profile.index') }}">Profilim</a></li>
                    @else
                        <li><a href="{{ route('login') }}">Giriş Yap</a></li>
                        <li><a href="{{ route('register') }}">Kayıt Ol</a></li>
                    @endauth
                </ul>
            </div>
            <div class="footer-section">
                <h4>İletişim</h4>
                <ul class="contact-info">
                    <li><i class="fas fa-map-marker-alt"></i> İstanbul, TÜRKİYE</li>
                    <li><i class="fas fa-phone"></i> +90 551 128 63 22</li>
                    <li><i class="fas fa-envelope"></i> emirhanbilisim52@gmail.com</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'ZAMASON') }}. Tüm hakları saklıdır.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/main.js') }}?v={{ time() }}"></script>
    <script>
    // CSRF Token - Const kullanarak daha güvenli
    (function() {
        'use strict';

        const CSRF_TOKEN = '{{ csrf_token() }}';

        // Sadece gerekli yerlere expose et
        window.getCsrfToken = function() {
            return CSRF_TOKEN;
        };
    })();

    // Favori toggle fonksiyonu - AJAX (sayfa yenilenmeden)
    async function toggleFavori(btn, urunId) {
        if (btn.disabled) return;

        btn.disabled = true;
        const originalIcon = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const response = await fetch('{{ route("favorites.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ urun_id: urunId })
            });

            const data = await response.json();

            if (data.success) {
                // Buton durumunu güncelle
                if (data.favoride) {
                    btn.classList.add('active');
                    btn.innerHTML = '<i class="fas fa-heart"></i>';
                    btn.title = 'Favorilerden Çıkar';
                } else {
                    btn.classList.remove('active');
                    btn.innerHTML = '<i class="far fa-heart"></i>';
                    btn.title = 'Favorilere Ekle';
                }

                // Toast göster
                showFavoriToast(data.favoride, data.message);
            } else {
                btn.innerHTML = originalIcon;
                showFavoriToast(false, data.message || 'Bir hata oluştu', true);
            }
        } catch (error) {
            console.error('Favori hatası:', error);
            btn.innerHTML = originalIcon;
            showFavoriToast(false, 'Bağlantı hatası', true);
        }

        btn.disabled = false;
    }

    // Favori toast bildirimi
    function showFavoriToast(added, message, isError = false) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.cssText = 'position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 350px;';
            document.body.appendChild(container);
        }

        const bgColor = isError ? '#ef4444' : (added ? '#10b981' : '#f59e0b');
        const icon = isError ? 'fa-times-circle' : (added ? 'fa-heart' : 'fa-heart-broken');

        const toast = document.createElement('div');
        toast.style.cssText = `background: #fff; border-radius: 12px; padding: 15px; margin-bottom: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 12px; animation: slideIn 0.3s ease; border-left: 4px solid ${bgColor};`;
        toast.innerHTML = `
            <div style="width: 40px; height: 40px; background: ${bgColor}15; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                <i class="fas ${icon}" style="color: ${bgColor}; font-size: 18px;"></i>
            </div>
            <div style="flex: 1;">
                <strong style="color: #333; font-size: 0.9rem;">${added ? 'Favorilere Eklendi' : (isError ? 'Hata' : 'Favorilerden Çıkarıldı')}</strong>
                <p style="color: #666; font-size: 0.8rem; margin: 3px 0 0 0;">${message}</p>
            </div>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #999; cursor: pointer; font-size: 1.2rem;">&times;</button>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }


    // Gerçek zamanlı bildirim ve mesaj sayısı güncellemesi
    @auth
    (function() {
        // Görülen bildirim ID'lerini sakla
        let seenNotificationIds = new Set();
        let lastNotificationId = null;

        // Badge güncelleme fonksiyonu
        function updateBadge(selector, count) {
            const badge = document.querySelector(selector);
            const navLink = badge ? badge.closest('.nav-link') : document.querySelector('a[href*="bildirimler"], a[href*="sohbet"]');

            if (count > 0) {
                if (badge) {
                    badge.textContent = count > 99 ? '99+' : count;
                } else if (navLink) {
                    const newBadge = document.createElement('span');
                    newBadge.className = selector.replace('.', '');
                    newBadge.textContent = count > 99 ? '99+' : count;
                    navLink.appendChild(newBadge);
                }
            } else if (badge) {
                badge.remove();
            }
        }

        // Sayıları güncelle
        function fetchCounts() {
            console.log('[Bildirim] Polling başlatıldı...');

            // Mesaj sayısı
            fetch('/api/unread-message-count', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            })
                .then(res => res.json())
                .then(data => {
                    console.log('[Mesaj] Sayı:', data.sayi);
                    updateBadge('.message-count', data.sayi);
                })
                .catch(err => console.error('[Mesaj] Hata:', err));

            // Bildirim sayısı ve son bildirim
            fetch('/notifications/unread-count', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            })
                .then(res => {
                    console.log('[Bildirim] Response status:', res.status);
                    return res.json();
                })
                .then(data => {
                    console.log('[Bildirim] Data:', data);
                    console.log('[Bildirim] Son bildirim:', data.sonBildirim);
                    console.log('[Bildirim] Görülenler:', Array.from(seenNotificationIds));
                    console.log('[Bildirim] Son ID:', lastNotificationId);

                    updateBadge('.notification-count', data.sayi);

                    // Yeni bildirim varsa ve daha önce gösterilmemişse toast göster
                    if (data.sonBildirim && data.sonBildirim.id) {
                        const notifId = data.sonBildirim.id;
                        console.log('[Bildirim] Kontrol - notifId:', notifId, 'seen:', seenNotificationIds.has(notifId), 'last:', lastNotificationId);

                        if (!seenNotificationIds.has(notifId)) {
                            console.log('[Bildirim] YENİ BİLDİRİM! Toast gösteriliyor...');
                            showNotificationToast(data.sonBildirim);
                            seenNotificationIds.add(notifId);
                            lastNotificationId = notifId;
                        }
                    }
                })
                .catch(err => console.error('[Bildirim] Hata:', err));
        }

        // Toast bildirim göster
        function showNotificationToast(bildirim) {
            if (!bildirim) return;

            // Toast container yoksa oluştur
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.style.cssText = 'position: fixed; top: 80px; right: 20px; z-index: 9999; max-width: 350px;';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className = 'notification-toast';
            toast.style.cssText = 'background: var(--card-bg); border-radius: 12px; padding: 15px; margin-bottom: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.15); display: flex; align-items: flex-start; gap: 12px; animation: slideIn 0.3s ease; cursor: pointer; border-left: 4px solid var(--primary);';
            toast.innerHTML = `
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                    <i class="fas fa-bell"></i>
                </div>
                <div style="flex: 1;">
                    <strong style="color: var(--text-primary); font-size: 0.9rem;">${bildirim.title || 'Yeni Bildirim'}</strong>
                    <p style="color: var(--text-secondary); font-size: 0.85rem; margin: 5px 0 0 0;">${bildirim.message || ''}</p>
                </div>
                <button onclick="this.parentElement.remove()" style="background: none; border: none; color: var(--text-light); cursor: pointer; font-size: 1.2rem;">&times;</button>
            `;

            if (bildirim.link) {
                toast.onclick = function(e) {
                    if (e.target.tagName !== 'BUTTON') {
                        window.location.href = bildirim.link;
                    }
                };
            }

            container.appendChild(toast);

            // 5 saniye sonra kaldır
            setTimeout(() => {
                toast.style.animation = 'slideOut 0.3s ease forwards';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // CSS animasyonları ekle
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
            @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
        `;
        document.head.appendChild(style);

        // İlk yüklemede mevcut TÜM okunmamış bildirimleri kaydet (sayfa açılışında toast gösterme)
        @php
            $unreadNotifs = auth()->user()->unreadNotifications()->pluck('id')->toArray();
        @endphp
        @foreach($unreadNotifs as $notifId)
        seenNotificationIds.add('{{ $notifId }}');
        @endforeach
        @if(count($unreadNotifs) > 0)
        lastNotificationId = '{{ $unreadNotifs[0] }}';
        @endif

        console.log('[Bildirim] Başlangıç - görülen IDler:', Array.from(seenNotificationIds));

        // İlk çağrıyı 2 saniye sonra yap
        setTimeout(fetchCounts, 2000);

        // Her 5 saniyede güncelle (daha hızlı bildirim için)
        setInterval(fetchCounts, 5000);
    })();
    @endauth
    </script>
    @stack('scripts')

    <!-- Theme Toggle Button -->
    <button class="theme-toggle" onclick="toggleTheme()" title="Tema Değiştir">
        <i class="fas fa-moon icon-moon"></i>
        <i class="fas fa-sun icon-sun"></i>
    </button>

    <script>
    // Tema değiştirme fonksiyonu
    function toggleTheme() {
        const html = document.documentElement;
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    }

    // Global Toast Sistemi
    (function() {
        // Toast container oluştur
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px;';
            document.body.appendChild(toastContainer);
        }

        // Toast gösterme fonksiyonu
        window.showToast = function(message, type = 'info', duration = 3000) {
            const toast = document.createElement('div');
            toast.className = 'toast-message toast-' + type;

            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };

            const colors = {
                success: { bg: '#d4edda', border: '#28a745', text: '#155724' },
                error: { bg: '#f8d7da', border: '#dc3545', text: '#721c24' },
                warning: { bg: '#fff3cd', border: '#ffc107', text: '#856404' },
                info: { bg: '#d1ecf1', border: '#17a2b8', text: '#0c5460' }
            };

            const color = colors[type] || colors.info;
            const icon = icons[type] || icons.info;

            toast.innerHTML = '<i class="fas ' + icon + '"></i> <span>' + message + '</span> <button class="toast-close">&times;</button>';
            toast.style.cssText = 'display: flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); animation: toastSlideIn 0.3s ease; background: ' + color.bg + '; border-left: 4px solid ' + color.border + '; color: ' + color.text + '; font-size: 0.9rem; max-width: 350px;';

            // Dark mode desteği
            if (document.documentElement.getAttribute('data-theme') === 'dark') {
                toast.style.background = '#2d2d44';
                toast.style.color = '#e0e0e0';
            }

            toastContainer.appendChild(toast);

            // Kapatma butonu
            toast.querySelector('.toast-close').onclick = function() {
                closeToast(toast);
            };

            // Otomatik kapanma
            if (duration > 0) {
                setTimeout(() => closeToast(toast), duration);
            }

            return toast;
        };

        function closeToast(toast) {
            toast.style.animation = 'toastSlideOut 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }

        // CSS ekle
        const style = document.createElement('style');
        style.textContent = `
            @keyframes toastSlideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
            @keyframes toastSlideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
            .toast-close { background: none; border: none; font-size: 1.2rem; cursor: pointer; opacity: 0.6; margin-left: auto; color: inherit; }
            .toast-close:hover { opacity: 1; }
        `;
        document.head.appendChild(style);
    })();

    // Global Confirm Modal Sistemi
    (function() {
        // Modal HTML oluştur
        const modalHtml = `
            <div id="confirm-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
                <div id="confirm-modal" style="background: var(--card-bg, #fff); border-radius: 16px; padding: 24px; max-width: 400px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.2); animation: modalFadeIn 0.2s ease;">
                    <div id="confirm-modal-icon" style="width: 60px; height: 60px; border-radius: 50%; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; font-size: 24px;"></div>
                    <h3 id="confirm-modal-title" style="text-align: center; color: var(--text-primary, #333); margin-bottom: 12px; font-size: 1.2rem;"></h3>
                    <p id="confirm-modal-message" style="text-align: center; color: var(--text-secondary, #666); margin-bottom: 24px; font-size: 0.95rem;"></p>
                    <div style="display: flex; gap: 12px; justify-content: center;">
                        <button id="confirm-modal-cancel" style="padding: 10px 24px; border-radius: 8px; border: 1px solid var(--border-color, #ddd); background: var(--bg-secondary, #f5f5f5); color: var(--text-primary, #333); font-size: 0.95rem; cursor: pointer; transition: all 0.2s;">İptal</button>
                        <button id="confirm-modal-ok" style="padding: 10px 24px; border-radius: 8px; border: none; font-size: 0.95rem; cursor: pointer; transition: all 0.2s; color: #fff;"></button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Modal CSS
        const modalStyle = document.createElement('style');
        modalStyle.textContent = `
            @keyframes modalFadeIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
            @keyframes modalFadeOut { from { transform: scale(1); opacity: 0; } to { transform: scale(0.9); opacity: 0; } }
            #confirm-modal-cancel:hover { background: var(--bg-hover, #e0e0e0) !important; }
            #confirm-modal-ok:hover { filter: brightness(1.1); }
            [data-theme="dark"] #confirm-modal { background: #1e1e2e; }
            [data-theme="dark"] #confirm-modal-title { color: #e0e0e0; }
            [data-theme="dark"] #confirm-modal-message { color: #a0a0a0; }
            [data-theme="dark"] #confirm-modal-cancel { background: #2d2d44; border-color: #3d3d54; color: #e0e0e0; }
        `;
        document.head.appendChild(modalStyle);

        const overlay = document.getElementById('confirm-modal-overlay');
        const modal = document.getElementById('confirm-modal');
        const iconEl = document.getElementById('confirm-modal-icon');
        const titleEl = document.getElementById('confirm-modal-title');
        const messageEl = document.getElementById('confirm-modal-message');
        const cancelBtn = document.getElementById('confirm-modal-cancel');
        const okBtn = document.getElementById('confirm-modal-ok');

        let resolvePromise = null;

        // Confirm gösterme fonksiyonu
        window.showConfirm = function(options) {
            return new Promise((resolve) => {
                resolvePromise = resolve;

                const type = options.type || 'warning';
                const title = options.title || 'Onay';
                const message = options.message || 'Bu işlemi gerçekleştirmek istediğinizden emin misiniz?';
                const confirmText = options.confirmText || 'Evet';
                const cancelText = options.cancelText || 'İptal';

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
                cancelBtn.textContent = cancelText;
                okBtn.textContent = confirmText;
                okBtn.style.background = config.color;

                overlay.style.display = 'flex';
                okBtn.focus();
            });
        };

        // Event listeners
        cancelBtn.onclick = function() {
            overlay.style.display = 'none';
            if (resolvePromise) resolvePromise(false);
        };

        okBtn.onclick = function() {
            overlay.style.display = 'none';
            if (resolvePromise) resolvePromise(true);
        };

        overlay.onclick = function(e) {
            if (e.target === overlay) {
                overlay.style.display = 'none';
                if (resolvePromise) resolvePromise(false);
            }
        };

        // ESC tuşu ile kapatma
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay.style.display === 'flex') {
                overlay.style.display = 'none';
                if (resolvePromise) resolvePromise(false);
            }
        });
    })();
    </script>
</body>
</html>
