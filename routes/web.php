<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\BecomeSellerController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

// Storage images route (Windows hosting symlink workaround)
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    $mimeType = mime_content_type($fullPath);
    return Response::file($fullPath, ['Content-Type' => $mimeType]);
})->where('path', '.*')->name('storage.serve');

// Maintenance mode test (temporary - will be removed later)
Route::get('/bakim-test', function () {
    $setting = \Illuminate\Support\Facades\DB::table('site_settings')
        ->where('key', 'maintenance_mode')
        ->first();

    $isAdmin = false;
    $userType = null;
    if (auth()->check()) {
        $userType = auth()->user()->kullanici_tipi;
        $isAdmin = in_array($userType, ['admin', 'super_admin']);
    }

    $shouldShowMaintenance = $setting && $setting->value === '1' && !$isAdmin;

    return response()->json([
        'maintenance_mode_value' => $setting?->value,
        'is_logged_in' => auth()->check(),
        'user_type' => $userType,
        'is_admin' => $isAdmin,
        'should_show_maintenance' => $shouldShowMaintenance,
        'controller_file_time' => filemtime(app_path('Http/Controllers/HomeController.php')),
    ]);
});

// =============================================
// 301 REDIRECTS (SEO - Old Turkish URLs to New English URLs)
// =============================================
Route::redirect('/giris', '/login', 301);
Route::redirect('/kayit', '/register', 301);
Route::redirect('/cikis', '/logout', 301);
Route::redirect('/dogrula/{token}', '/verify/{token}', 301)->where('token', '.*');
Route::redirect('/yeniden-gonder', '/resend-verification', 301);
Route::redirect('/sifremi-unuttum', '/forgot-password', 301);
Route::redirect('/sifre-sifirla/{token}', '/reset-password/{token}', 301)->where('token', '.*');
Route::redirect('/urunler', '/products', 301);
Route::redirect('/urunler/{urun}', '/products/{urun}', 301)->where('urun', '.*');
Route::redirect('/sohbet', '/chat', 301);
Route::redirect('/sohbet/{konusma}', '/chat/{konusma}', 301)->where('konusma', '.*');
Route::redirect('/bildirimler', '/notifications', 301);
Route::redirect('/favorilerim', '/my-favorites', 301);
Route::redirect('/profilim', '/profile', 301);
Route::redirect('/satici-ol', '/become-seller', 301);
Route::redirect('/satici/panel', '/seller/dashboard', 301);
Route::redirect('/satici/urun-ekle', '/seller/product/create', 301);
Route::redirect('/kargo', '/shipping', 301);
Route::redirect('/sepet', '/cart', 301);
Route::redirect('/odeme', '/payment', 301);

// =============================================
// MAIN ROUTES
// =============================================

// Home Page (with maintenance mode check)
Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('maintenance');

// Products (Everyone can see - with maintenance mode check)
Route::middleware('maintenance')->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{urun}', [ProductController::class, 'show'])->name('products.show');
});

// Blog
Route::get('/blog', function () {
    return view('blog.index');
})->name('blog');

// Auth Routes (For Guests)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Password Reset
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

// Email Verification Routes
Route::get('/verify/{token}', [VerificationController::class, 'verify'])->name('verify');
Route::get('/resend-verification', [VerificationController::class, 'resend'])->name('resend-verification');

// 2FA Verification (During Login)
Route::get('/2fa-verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
Route::post('/2fa-verify', [TwoFactorController::class, 'verifyCode'])->name('2fa.verify.code');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Authenticated Users
Route::middleware('auth')->group(function () {
    // Old /mesajlar/ links redirect to /chat/
    Route::get('/mesajlar/{konusma}', function ($konusma) {
        return redirect("/chat/{$konusma}");
    });

    // Chat Routes
    Route::prefix('chat')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('chat.index');
        Route::post('/start', [ChatController::class, 'store'])->name('chat.store');

        // Messages (AJAX) - IMPORTANT: Must be BEFORE {konusma} route
        Route::post('/message/{id}/delete', [MessageController::class, 'destroy'])->name('message.delete');
        Route::post('/message/{id}/edit', [MessageController::class, 'update'])->name('message.edit');
        Route::post('/{konusma}/message', [MessageController::class, 'store'])->name('message.store');
        Route::get('/{konusma}/new-messages', [MessageController::class, 'getNewMessages'])->name('message.new');
        Route::post('/{konusma}/mark-read', [MessageController::class, 'markAsRead'])->name('message.read');

        // Archived must come before {konusma} wildcard
        Route::get('/archived', [ChatController::class, 'archived'])->name('chat.archived');

        // Show conversation (wildcard - must be last)
        Route::get('/{konusma}', [ChatController::class, 'show'])->name('chat.show');
        Route::post('/{konusma}/archive', [ChatController::class, 'archive'])->name('chat.archive');
        Route::post('/{konusma}/unarchive', [ChatController::class, 'unarchive'])->name('chat.unarchive');
        Route::delete('/{konusma}/clear', [ChatController::class, 'clear'])->name('chat.clear');

        // Offers
        Route::post('/{konusma}/offer', [OfferController::class, 'store'])->name('offer.store');
        Route::post('/offer/{teklif}/accept', [OfferController::class, 'accept'])->name('offer.accept');
        Route::post('/offer/{teklif}/reject', [OfferController::class, 'reject'])->name('offer.reject');
        Route::post('/offer/{teklif}/cancel', [OfferController::class, 'cancel'])->name('offer.cancel');
    });

    // Unread message count API
    Route::get('/api/unread-message-count', function () {
        return response()->json([
            'count' => auth()->user()->okunmamisMesajSayisi(),
        ]);
    })->name('api.unread-messages');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/x123', [NotificationController::class, 'liste'])->name('notifications.list');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // Favorites
    Route::get('/my-favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::match(['get', 'post'], '/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/favorites/{urun}/remove', [FavoriteController::class, 'remove'])->name('favorites.remove');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 2FA (Two Factor Authentication)
    Route::get('/profile/2fa', [TwoFactorController::class, 'index'])->name('profile.2fa');
    Route::get('/profile/2fa/enable', [TwoFactorController::class, 'enable'])->name('profile.2fa.enable');
    Route::post('/profile/2fa/confirm', [TwoFactorController::class, 'confirm'])->name('profile.2fa.confirm');
    Route::post('/profile/2fa/disable', [TwoFactorController::class, 'disable'])->name('profile.2fa.disable');
    Route::get('/profile/2fa/backup-codes', [TwoFactorController::class, 'showBackupCodes'])->name('profile.2fa.backup');
    Route::post('/profile/2fa/regenerate-backup-codes', [TwoFactorController::class, 'regenerateBackupCodes'])->name('profile.2fa.backup.regenerate');

    // Become Seller (only for buyers)
    Route::get('/become-seller', [BecomeSellerController::class, 'index'])->name('seller.become');
    Route::post('/become-seller', [BecomeSellerController::class, 'store'])->name('seller.become.store');

    // Reviews
    Route::post('/products/{urun}/review', [ReviewController::class, 'store'])->name('review.store');
    Route::put('/review/{yorum}', [ReviewController::class, 'update'])->name('review.update');
    Route::delete('/review/{yorum}', [ReviewController::class, 'destroy'])->name('review.destroy');
});

// Seller Routes
Route::middleware(['auth', 'seller'])->prefix('seller')->group(function () {
    Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('seller.dashboard');

    // Product Management
    Route::get('/product/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/product/create', [ProductController::class, 'store'])->name('products.store');
    Route::get('/product/{urun}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/product/{urun}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/product/{urun}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/product/{urun}/toggle-sold', [ProductController::class, 'toggleSold'])->name('products.toggle-sold');
});

// Address API (Province/District/Neighborhood)
Route::prefix('api/address')->group(function () {
    Route::get('/provinces', [AddressController::class, 'getProvinces'])->name('api.provinces');
    Route::get('/districts/{il}', [AddressController::class, 'getDistricts'])->name('api.districts');
    Route::get('/neighborhoods/{ilce}', [AddressController::class, 'getNeighborhoods'])->name('api.neighborhoods');
});

// Shipping Routes
Route::middleware('auth')->prefix('shipping')->group(function () {
    Route::get('/', [ShippingController::class, 'myShipments'])->name('shipping.index');
    Route::get('/{konusma}/create', [ShippingController::class, 'create'])->name('shipping.create')->whereNumber('konusma');
    Route::post('/{konusma}/create', [ShippingController::class, 'store'])->name('shipping.store')->whereNumber('konusma');
    Route::get('/{kargo}', [ShippingController::class, 'show'])->name('shipping.show')->whereNumber('kargo');
    Route::post('/{kargo}/address', [ShippingController::class, 'saveAddress'])->name('shipping.address');
    Route::post('/{kargo}/tracking-number', [ShippingController::class, 'saveTrackingNumber'])->name('shipping.tracking');
    Route::post('/{kargo}/delivered', [ShippingController::class, 'markDelivered'])->name('shipping.delivered');
    Route::post('/calculate-fee', [ShippingController::class, 'calculateFee'])->name('shipping.calculate-fee');
});

// Cart Routes
Route::middleware('auth')->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add/{teklif}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/count', [CartController::class, 'count'])->name('cart.count');
});

// Payment Callback and Success - outside auth middleware (for iyzico redirect)
Route::match(['get', 'post'], '/payment/callback', [PaymentController::class, 'callback'])
    ->name('payment.callback')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/payment/success/{odeme}', [PaymentController::class, 'success'])->name('payment.success');

// Payment Routes (auth required)
Route::middleware('auth')->prefix('payment')->group(function () {
    Route::get('/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::post('/initiate', [PaymentController::class, 'initiate'])->name('payment.initiate');
    Route::get('/detail/{odeme}', [PaymentController::class, 'detail'])->name('payment.detail');
    Route::get('/my-orders', [PaymentController::class, 'list'])->name('payment.list');
});

// Category Attributes API
Route::get('/api/category/{kategori}/attributes', [ProductController::class, 'getCategoryAttributes'])
    ->name('api.category.attributes');

// Review API
Route::get('/api/products/{urun}/reviews', [ReviewController::class, 'getProductReviews'])
    ->name('api.product.reviews');

// =============================================
// ADMIN ROUTES
// =============================================
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\IpManagementController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\SliderController as AdminSliderController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\ProductManagementController;
use App\Http\Controllers\Admin\ListManagementController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifications', [AdminDashboardController::class, 'notifications'])->name('notifications');

    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create');
        Route::post('/create', [UserManagementController::class, 'store'])->name('store');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('show');
        Route::post('/{user}/ban', [UserManagementController::class, 'ban'])->name('ban');
        Route::post('/{user}/unban', [UserManagementController::class, 'unban'])->name('unban');
        Route::post('/{user}/change-type', [UserManagementController::class, 'changeTip'])->name('changeTip');
        Route::post('/{user}/assign-role', [UserManagementController::class, 'assignRole'])->name('assignRole');
        Route::post('/{user}/remove-role', [UserManagementController::class, 'removeRole'])->name('removeRole');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
    });

    // IP Management
    Route::prefix('ip')->name('ip.')->group(function () {
        Route::get('/', [IpManagementController::class, 'index'])->name('index');
        Route::get('/bans', [IpManagementController::class, 'bans'])->name('bans');
        Route::get('/search', [IpManagementController::class, 'search'])->name('search');
        Route::get('/{ip}', [IpManagementController::class, 'show'])->name('show')->where('ip', '[0-9.:]+');
        Route::post('/ban', [IpManagementController::class, 'ban'])->name('ban');
        Route::post('/{ip}/unban', [IpManagementController::class, 'unban'])->name('unban')->where('ip', '[0-9.:]+');
    });

    // Site Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AdminSettingsController::class, 'index'])->name('index');
        Route::post('/', [AdminSettingsController::class, 'update'])->name('update');
        Route::post('/create', [AdminSettingsController::class, 'store'])->name('store');
        Route::post('/clear-cache', [AdminSettingsController::class, 'clearCache'])->name('clearCache');
        Route::delete('/{setting}', [AdminSettingsController::class, 'destroy'])->name('destroy');
    });

    // Role and Permission Management (Super Admin Only)
    Route::middleware('permission:manage_roles')->prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RolePermissionController::class, 'index'])->name('index');
        Route::get('/create', [RolePermissionController::class, 'create'])->name('create');
        Route::post('/', [RolePermissionController::class, 'store'])->name('store');
        Route::get('/{role}', [RolePermissionController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RolePermissionController::class, 'update'])->name('update');
        Route::delete('/{role}', [RolePermissionController::class, 'destroy'])->name('destroy');
    });

    // Activity Logs
    Route::get('/activities', function () {
        $aktiviteler = \App\Models\AdminActivityLog::with('admin')
            ->latest('created_at')
            ->paginate(50);
        return view('admin.activities.index', compact('aktiviteler'));
    })->name('activities.index');

    // Slider Management
    Route::prefix('sliders')->name('sliders.')->group(function () {
        Route::get('/', [AdminSliderController::class, 'index'])->name('index');
        Route::get('/create', [AdminSliderController::class, 'create'])->name('create');
        Route::post('/', [AdminSliderController::class, 'store'])->name('store');
        Route::get('/{slider}/edit', [AdminSliderController::class, 'edit'])->name('edit');
        Route::put('/{slider}', [AdminSliderController::class, 'update'])->name('update');
        Route::delete('/{slider}', [AdminSliderController::class, 'destroy'])->name('destroy');
    });

    // Category Management
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index'])->name('index');
        Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');
        Route::post('/', [AdminCategoryController::class, 'store'])->name('store');
        Route::get('/{kategori}/edit', [AdminCategoryController::class, 'edit'])->name('edit');
        Route::put('/{kategori}', [AdminCategoryController::class, 'update'])->name('update');
        Route::delete('/{kategori}', [AdminCategoryController::class, 'destroy'])->name('destroy');
    });

    // Review Management
    Route::prefix('reviews')->name('reviews.')->group(function () {
        Route::get('/', [AdminReviewController::class, 'index'])->name('index');
        Route::post('/{yorum}/approve', [AdminReviewController::class, 'approve'])->name('approve');
        Route::post('/{yorum}/reject', [AdminReviewController::class, 'reject'])->name('reject');
        Route::post('/bulk-approve', [AdminReviewController::class, 'bulkApprove'])->name('bulkApprove');
        Route::post('/bulk-delete', [AdminReviewController::class, 'bulkDelete'])->name('bulkDelete');
    });

    // Product Management
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductManagementController::class, 'index'])->name('index');
        Route::get('/pending', [ProductManagementController::class, 'pending'])->name('pending');
        Route::match(['get', 'post', 'delete'], '/bulk-action', [ProductManagementController::class, 'bulkAction'])->name('bulk');
        Route::get('/{urun}', [ProductManagementController::class, 'show'])->name('show')->whereNumber('urun');
        Route::get('/{urun}/edit', [ProductManagementController::class, 'edit'])->name('edit')->whereNumber('urun');
        Route::put('/{urun}', [ProductManagementController::class, 'update'])->name('update')->whereNumber('urun');
        Route::post('/{urun}/approve', [ProductManagementController::class, 'approve'])->name('approve')->whereNumber('urun');
        Route::post('/{urun}/reject', [ProductManagementController::class, 'reject'])->name('reject')->whereNumber('urun');
        Route::post('/{urun}/deactivate', [ProductManagementController::class, 'deactivate'])->name('deactivate')->whereNumber('urun');
        Route::delete('/{urun}', [ProductManagementController::class, 'destroy'])->name('destroy')->whereNumber('urun');
    });

    // List Management (Blacklist / Whitelist)
    Route::prefix('lists')->name('lists.')->group(function () {
        Route::get('/', [ListManagementController::class, 'index'])->name('index');

        // IP List
        Route::get('/ip', [ListManagementController::class, 'ipList'])->name('ip');
        Route::post('/ip', [ListManagementController::class, 'ipAdd'])->name('ip.add');
        Route::delete('/ip/{ip}', [ListManagementController::class, 'ipDelete'])->name('ip.delete');
        Route::post('/ip/{ip}/toggle', [ListManagementController::class, 'ipToggle'])->name('ip.toggle');

        // Banned Keywords
        Route::get('/keywords', [ListManagementController::class, 'keywordList'])->name('keywords');
        Route::post('/keywords', [ListManagementController::class, 'keywordAdd'])->name('keyword.add');
        Route::get('/keywords/{kelime}/edit', [ListManagementController::class, 'keywordEdit'])->name('keyword.edit');
        Route::put('/keywords/{kelime}', [ListManagementController::class, 'keywordUpdate'])->name('keyword.update');
        Route::delete('/keywords/{kelime}', [ListManagementController::class, 'keywordDelete'])->name('keyword.delete');
        Route::post('/keywords/{kelime}/toggle', [ListManagementController::class, 'keywordToggle'])->name('keyword.toggle');

        // Email Domains
        Route::get('/domains', [ListManagementController::class, 'domainList'])->name('domains');
        Route::post('/domains', [ListManagementController::class, 'domainAdd'])->name('domain.add');
        Route::delete('/domains/{domain}', [ListManagementController::class, 'domainDelete'])->name('domain.delete');
        Route::post('/domains/{domain}/toggle', [ListManagementController::class, 'domainToggle'])->name('domain.toggle');
        Route::post('/domains/add-temporary', [ListManagementController::class, 'addTemporaryDomains'])->name('domain.add-temporary');

        // User Blocks
        Route::get('/blocks', [ListManagementController::class, 'blockList'])->name('blocks');
        Route::delete('/blocks/{engel}', [ListManagementController::class, 'blockRemove'])->name('block.remove');
    });
});
