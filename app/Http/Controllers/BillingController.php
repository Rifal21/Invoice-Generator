<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

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

    protected function initMidtrans()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');
    }

    public function topup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'total_amount' => 'required|numeric|min:1',
        ]);

        $nominal = (float) $request->amount;
        $totalAmount = (float) $request->total_amount; // nominal + admin fee + ppn
        $orderId = 'TOPUP-' . time() . '-' . Auth::id();

        $this->initMidtrans();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $totalAmount, // total yang dibayar user
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
            'item_details' => [
                [
                    'id' => 'TOPUP-NOMINAL',
                    'price' => (int) $nominal,
                    'quantity' => 1,
                    'name' => 'Topup Saldo Aplikasi',
                ],
                [
                    'id' => 'ADMIN-FEE',
                    'price' => 10000,
                    'quantity' => 1,
                    'name' => 'Biaya Admin',
                ],
                [
                    'id' => 'PPN-11',
                    'price' => (int) round($nominal * 0.11),
                    'quantity' => 1,
                    'name' => 'PPN (11%)',
                ],
            ],
            'enabled_payments' => [
                'qris',         // QRIS (semua dompet digital via QR)
                'gopay',        // GoPay
                'shopeepay',    // ShopeePay
                'dana',         // DANA
                'ovo',          // OVO
                'bca_va',       // BCA Virtual Account
                'bni_va',       // BNI Virtual Account
                'bri_va',       // BRI Virtual Account
                'mandiri_bill', // Mandiri Bill
                'other_va',     // Bank lain Virtual Account
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            $setting = Setting::firstOrCreate(
                ['key' => 'app_balance'],
                ['value' => 0, 'type' => 'number', 'group' => 'billing']
            );
            $currentBalance = (float) $setting->value;

            $transaction = Transaction::create([
                'user_id' => Auth::id(),
                'type' => 'topup',
                'amount' => $nominal,
                'status' => 'pending',
                'balance_before' => $currentBalance,
                'balance_after' => $currentBalance,
                'reference_id' => $orderId,
                'snap_token' => $snapToken,
                'description' => "Topup via Midtrans (Nominal: Rp " . number_format($nominal, 0, ',', '.') .
                    ", Admin: Rp 10.000, PPN 11%: Rp " . number_format(round($nominal * 0.11), 0, ',', '.') .
                    ", Total Bayar: Rp " . number_format($totalAmount, 0, ',', '.') . ") - Order ID: $orderId",
            ]);

            return response()->json([
                'success'     => true,
                'snap_token'  => $snapToken,
                'order_id'    => $orderId,
                'transaction' => [
                    'id'          => $transaction->id,
                    'user_name'   => Auth::user()->name,
                    'date'        => now()->format('d M Y'),
                    'time'        => now()->format('H:i'),
                    'description' => Str::limit($transaction->description, 60),
                    'amount'      => $nominal,
                    'amount_fmt'  => '+Rp ' . number_format($nominal, 0, ',', '.'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function manualTopup(Request $request)
    {
        if (!Auth::user()->isBillingManager()) {
            abort(403, 'Hanya admin yang dapat melakukan topup manual.');
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

            $desc = "Global Topup Manual (Nominal: Rp " . number_format($topupAmount) . 
                   ", Admin: Rp " . number_format($adminFee) . 
                   ", PPN 11%: Rp " . number_format($ppn) . 
                   ", Total: Rp " . number_format($totalCharge) . ")";
            
            if ($request->description) {
                $desc .= " | Note: " . $request->description;
            }

            Transaction::create([
                'user_id' => Auth::id(),
                'type' => 'topup',
                'amount' => $topupAmount,
                'status' => 'success',
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'description' => $desc,
            ]);
        });

        return redirect()->back()->with('success', "Berhasil top up manual Rp " . number_format($topupAmount, 0, ',', '.') . " ke saldo aplikasi.");
    }
}
