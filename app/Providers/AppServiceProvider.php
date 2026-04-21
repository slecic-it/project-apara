<?php

namespace App\Providers;

use App\Support\BankPortalNotificationBuilder;
use App\Support\SlecicNotificationBuilder;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(191);

        View::composer('layouts.bank-header', function ($view) {
            $view->with('headerNotifications', app(BankPortalNotificationBuilder::class)->build());
        });

        View::composer('layouts.slecic-header', function ($view) {
            $view->with('headerNotifications', app(SlecicNotificationBuilder::class)->build());
        });
    }
}
