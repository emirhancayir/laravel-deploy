<?php

namespace App\Providers;

use App\Models\Konusma;
use App\Policies\KonusmaPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Production'da HTTPS zorla
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Policy kaydi
        Gate::policy(Konusma::class, KonusmaPolicy::class);
    }
}
