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
        // Only run for authenticated users and non-AJAX requests (to avoid overhead)
        if (!Auth::check() || $request->ajax() || $request->wantsJson()) {
            return $next($request);
        }

        // Check billing status
        $status = Setting::where('key', 'app_billing_status')->first()?->value ?? 'active';
        if ($status === 'disabled') {
            return $next($request);
        }

        // Just check the balance. Deduction is handled in the background by a Scheduled Job.
        $balance = (float) (Setting::where('key', 'app_balance')->first()?->value ?? 0);

        // If balance is exhausted, notify or restrict
        if ($balance <= 0 && !Auth::user()->isBillingManager()) {
            // Rifal should always have access to top up, others are blocked
            if (!$request->routeIs('billing.*') && !$request->routeIs('logout')) {
                return response()->redirectToRoute('billing.index')->with('error', 'Saldo aplikasi telah habis. Silakan hubungi Administrator (Rifal Kurniawan) untuk melakukan Top Up agar aplikasi dapat digunakan kembali.');
            }
        }

        return $next($request);
    }
}
