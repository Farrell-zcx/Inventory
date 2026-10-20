<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\StockTransaction;
use App\Observers\StockTransactionObserver;
use Illuminate\Support\Facades\Gate;
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
        if (config('app.env') === 'production' || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        StockTransaction::observe(StockTransactionObserver::class);

        Gate::define('manage-master-data', fn($user) => in_array($user->role, ['admin', 'staff']));
        Gate::define('delete-master-data', fn($user) => $user->role === 'admin');
        Gate::define('manage-suppliers', fn($user) => $user->role === 'admin');
        Gate::define('manage-transactions', fn($user) => in_array($user->role, ['admin', 'staff']));
        
        Gate::define('view-po', fn($user) => in_array($user->role, ['admin', 'staff']));
        Gate::define('create-po', fn($user) => in_array($user->role, ['admin', 'staff']));
        Gate::define('approve-po', fn($user) => $user->role === 'admin');
        
        Gate::define('view-reports', fn($user) => in_array($user->role, ['admin', 'staff']));
    }
}
