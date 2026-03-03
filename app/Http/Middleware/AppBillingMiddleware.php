<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class AppBillingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Lewati: unauthenticated, AJAX/JSON (tidak perlu redirect HTML), dan API calls
        if (!Auth::check() || $request->ajax() || $request->wantsJson()) {
            return $next($request);
        }

        // Selalu lewati route logout agar user bisa keluar kapanpun
        if ($request->routeIs('logout')) {
            return $next($request);
        }

        // Lewati jika billing dinonaktifkan (FREE ACCESS mode)
        $status = Setting::where('key', 'app_billing_status')->first()?->value ?? 'active';
        if ($status !== 'active') {
            return $next($request);
        }

        // Cek saldo
        $balance = (float) (Setting::where('key', 'app_balance')->first()?->value ?? 0);

        if ($balance <= 0) {
            // Billing Manager: boleh akses halaman billing untuk topup, tapi TIDAK bisa fitur lain
            if (Auth::user()->isBillingManager()) {
                if (!$request->routeIs('billing.*')) {
                    return redirect()->route('billing.suspended');
                }
                return $next($request);
            }

            // User biasa: hanya boleh akses halaman suspended
            if (!$request->routeIs('billing.suspended')) {
                return redirect()->route('billing.suspended');
            }
        }

        return $next($request);
    }
}
