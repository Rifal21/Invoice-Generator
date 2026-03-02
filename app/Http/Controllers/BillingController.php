<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index()
    {
        $appBalance = Setting::where('key', 'app_balance')->first()?->value ?? 0;
        $ratePerMinute = Setting::where('key', 'app_billing_rate_per_minute')->first()?->value ?? 40.0;
        $transactions = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('billing.index', compact('appBalance', 'transactions', 'ratePerMinute'));
    }

    public function manage()
    {
        if (!Auth::user()->isBillingManager()) {
            abort(403, 'Akses ditolak. Hanya Rifal Kurniawan yang dapat mengelola billing.');
        }

        $appBalance = Setting::where('key', 'app_balance')->first()?->value ?? 0;
        $ratePerMinute = Setting::where('key', 'app_billing_rate_per_minute')->first()?->value ?? 40.0;
        $billingStatus = Setting::where('key', 'app_billing_status')->first()?->value ?? 'active';
        
        $allTransactions = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('billing.manage', compact('appBalance', 'allTransactions', 'ratePerMinute', 'billingStatus'));
    }

    public function updateRate(Request $request)
    {
        if (!Auth::user()->isBillingManager()) {
            abort(403, 'Hanya Rifal Kurniawan yang dapat mengubah tarif.');
        }

        $request->validate([
            'rate_per_minute' => 'required|numeric|min:0',
        ]);

        Setting::updateOrCreate(
            ['key' => 'app_billing_rate_per_minute'],
            ['value' => $request->rate_per_minute, 'type' => 'number', 'group' => 'billing']
        );

        return redirect()->back()->with('success', "Tarif billing berhasil diperbarui menjadi Rp " . number_format($request->rate_per_minute, 0, ',', '.') . "/menit.");
    }

    public function updateStatus(Request $request)
    {
        if (!Auth::user()->isBillingManager()) {
            abort(403, 'Hanya Rifal Kurniawan yang dapat mengubah status billing.');
        }

        $request->validate([
            'status' => 'required|in:active,disabled',
        ]);

        Setting::updateOrCreate(
            ['key' => 'app_billing_status'],
            ['value' => $request->status, 'type' => 'string', 'group' => 'billing']
        );

        $msg = $request->status === 'active' ? 'Sistem billing telah DIAKTIFKAN.' : 'Sistem billing telah DINONAKTIFKAN.';
        return redirect()->back()->with('success', $msg);
    }

    public function topup(Request $request)
    {
        if (!Auth::user()->isBillingManager()) {
            abort(403, 'Hanya Rifal Kurniawan yang dapat melakukan topup.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255',
        ]);

        $topupAmount = (float) $request->amount;
        $adminFee = 10000;
        $ppn = $topupAmount * 0.11;
        $totalCharge = $topupAmount + $adminFee + $ppn;

        DB::transaction(function () use ($topupAmount, $adminFee, $ppn, $totalCharge, $request) {
            $setting = Setting::firstOrCreate(
                ['key' => 'app_balance'],
                ['value' => 0, 'type' => 'number', 'group' => 'billing']
            );

            $balanceBefore = (float) $setting->value;
            $balanceAfter = $balanceBefore + $topupAmount;

            $setting->update(['value' => $balanceAfter]);

            $desc = "Global Topup (Nominal: Rp " . number_format($topupAmount) . 
                   ", Admin: Rp " . number_format($adminFee) . 
                   ", PPN 11%: Rp " . number_format($ppn) . 
                   ", Total Charge: Rp " . number_format($totalCharge) . ")";
            
            if ($request->description) {
                $desc .= " | Note: " . $request->description;
            }

            Transaction::create([
                'user_id' => Auth::id(),
                'type' => 'topup',
                'amount' => $topupAmount,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $desc,
            ]);
        });

        return redirect()->back()->with('success', "Berhasil top up Rp " . number_format($topupAmount, 0, ',', '.') . " ke saldo aplikasi.");
    }
}
