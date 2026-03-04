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
            View::composer(['layouts.*', 'invoices.*', 'profit.*', 'billing.*'], function ($view) {
                $settings = Setting::all()->pluck('value', 'key');
                
                // Calculate "live" balance to prevent jumpy UI on refresh
                $status = $settings['app_billing_status'] ?? 'active';
                if ($status === 'active' && isset($settings['app_balance'], $settings['app_billing_rate_per_minute'], $settings['app_billing_last_updated_at'])) {
                    $balance = (float) $settings['app_balance'];
                    $rate = (float) $settings['app_billing_rate_per_minute'];
                    $lastUpdate = \Carbon\Carbon::parse($settings['app_billing_last_updated_at']);
                    
                    // Only calculate if the last update was in the past
                    if ($lastUpdate->isPast()) {
                        $secondsPassed = now()->floatDiffInSeconds($lastUpdate);
                        $deduction = ($secondsPassed * ($rate / 60));
                        $settings['app_balance'] = max(0, $balance - $deduction);
                    }
                }
                
                $view->with('site_settings', $settings);
            });
        }
        
        if (Schema::hasTable('sidebar_items')) {
            View::composer(['layouts.partials.sidebar', 'layouts.partials.menu-items', 'layouts.partials.mobile-menu'], function ($view) {
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
