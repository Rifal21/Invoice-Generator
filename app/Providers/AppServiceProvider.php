<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use App\Models\SidebarItem;

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
        Paginator::useTailwind();
        if (str_contains(config('app.url'), 'https://') || $this->app->environment('production')) {
            URL::forceScheme('https');
        }

        if (Schema::hasTable('settings')) {
            View::composer(['layouts.*', 'invoices.*', 'profit.*'], function ($view) {
                $settings = Setting::all()->pluck('value', 'key');
                $view->with('site_settings', $settings);
            });
        }
        
        if (Schema::hasTable('sidebar_items')) {
            View::composer(['layouts.partials.sidebar', 'layouts.partials.menu-items'], function ($view) {
                $sidebarItems = SidebarItem::whereNull('parent_id')
                    ->where('is_active', true)
                    ->with(['children' => function($q) {
                        $q->where('is_active', true);
                    }])
                    ->orderBy('order')
                    ->get();
                $view->with('sidebarItems', $sidebarItems);
            });
        }
    }
}
