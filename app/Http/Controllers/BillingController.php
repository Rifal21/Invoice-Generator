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
        $appBalance   = Setting::where('key', 'app_balance')->first()?->value ?? 0;
        $ratePerMinute = Setting::where('key', 'app_billing_rate_per_minute')->first()?->value ?? 40.0;
        $qrisImage    = Setting::where('key', 'billing_qris_image')->first()?->value;
        $qrisWaNotify = Setting::where('key', 'billing_qris_wa_notify')->first()?->value;
        $transactions = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('billing.index', compact('appBalance', 'transactions', 'ratePerMinute', 'qrisImage', 'qrisWaNotify'));
    }

    public function manage()
    {
        if (!Auth::user()->isBillingManager()) {
            abort(403, 'Akses ditolak. Hanya Rifal Kurniawan yang dapat mengelola billing.');
        }

        $appBalance    = Setting::where('key', 'app_balance')->first()?->value ?? 0;
        $ratePerMinute = Setting::where('key', 'app_billing_rate_per_minute')->first()?->value ?? 40.0;
        $billingStatus = Setting::where('key', 'app_billing_status')->first()?->value ?? 'active';
        $qrisImage     = Setting::where('key', 'billing_qris_image')->first()?->value;
        $qrisWaNotify  = Setting::where('key', 'billing_qris_wa_notify')->first()?->value;

        $allTransactions = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Ambil semua transaksi QRIS yang masih PENDING untuk dikonfirmasi
        $pendingQris = Transaction::with('user')
            ->where('status', 'pending')
            ->where('payment_channel', 'qris')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('billing.manage', compact('appBalance', 'allTransactions', 'ratePerMinute', 'billingStatus', 'qrisImage', 'qrisWaNotify', 'pendingQris'));
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

    /**
     * User mengajukan topup via QRIS manual.
     * Menyimpan transaksi pending dan mengirim notifikasi WA ke admin.
     */
    public function qrisTopup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);

        $nominal     = (float) $request->amount;
        $adminFee    = 10000;
        $ppn         = $nominal * 0.11;
        $totalAmount = $nominal + $adminFee + $ppn;
        $orderId     = 'QRIS-' . time() . '-' . Auth::id();

        // Ambil saldo saat ini untuk balance_before/balance_after (pending = balance tidak berubah)
        $currentBalance = (float) (Setting::where('key', 'app_balance')->first()?->value ?? 0);

        $transaction = Transaction::create([
            'user_id'         => Auth::id(),
            'type'            => 'topup',
            'status'          => 'pending',
            'amount'          => $nominal,
            'balance_before'  => $currentBalance,
            'balance_after'   => $currentBalance, // belum berubah, admin yang akan konfirmasi
            'reference_id'    => $orderId,
            'payment_channel' => 'qris',
            'description'     => 'Top Up via QRIS Manual | Saldo: Rp ' . number_format($nominal, 0, ',', '.')
                               . ' | Admin: Rp ' . number_format($adminFee, 0, ',', '.')
                               . ' | PPN 11%: Rp ' . number_format($ppn, 0, ',', '.')
                               . ' | Total Transfer: Rp ' . number_format($totalAmount, 0, ',', '.'),
        ]);

        // Kirim notifikasi WA ke admin (Rifal)
        $waNumber = Setting::where('key', 'billing_qris_wa_notify')->first()?->value;
        if ($waNumber) {
            try {
                $phone = preg_replace('/[^0-9]/', '', $waNumber);
                if (str_starts_with($phone, '0')) {
                    $phone = '62' . substr($phone, 1);
                }

                $apiUrl  = env('WHATSAPP_API_URL');
                $apiKey  = env('WHATSAPP_API_KEY');
                $session = env('WHATSAPP_SESSION', 'default');

                $message = "🔔 *PERMINTAAN TOP UP QRIS*\n\n"
                    . "👤 *Dari:* " . Auth::user()->name . "\n"
                    . "💰 *Saldo Ditambahkan:* Rp " . number_format($nominal, 0, ',', '.') . "\n"
                    . "🧾 *Rincian Biaya:*\n"
                    . "   • Biaya Admin: Rp " . number_format($adminFee, 0, ',', '.') . "\n"
                    . "   • PPN (11%): Rp " . number_format($ppn, 0, ',', '.') . "\n"
                    . "💵 *Total yang Ditransfer: Rp " . number_format($totalAmount, 0, ',', '.') . "*\n"
                    . "🆔 *Order ID:* `{$orderId}`\n"
                    . "🕐 *Waktu:* " . now()->format('d-m-Y H:i') . "\n\n"
                    . "Silakan konfirmasi pembayaran dan input saldo secara manual di panel billing.";

                \Illuminate\Support\Facades\Http::withHeaders([
                    'X-Api-Key'    => $apiKey,
                    'Content-Type' => 'application/json',
                ])->post("{$apiUrl}/api/sendText", [
                    'session' => $session,
                    'chatId'  => $phone . '@c.us',
                    'text'    => $message,
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('[QRIS Topup] Gagal kirim notif WA: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success'        => true,
            'order_id'       => $orderId,
            'qris_image_url' => ($qrisPath = Setting::where('key', 'billing_qris_image')->first()?->value)
                                ? \Illuminate\Support\Facades\Storage::url($qrisPath)
                                : null,
            'total_amount'   => $totalAmount,
            'nominal'       => $nominal,
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
    }

    /**
     * Admin update QRIS image dan nomor WA notif.
     */
    public function updateQrisSettings(Request $request)
    {
        if (!Auth::user()->isBillingManager()) {
            abort(403);
        }

        $request->validate([
            'qris_image'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'qris_wa_notify' => 'nullable|string|max:20',
        ]);

        // Upload gambar QRIS
        if ($request->hasFile('qris_image')) {
            $path = $request->file('qris_image')->store('billing/qris', 'public');
            Setting::updateOrCreate(
                ['key' => 'billing_qris_image'],
                ['value' => $path, 'type' => 'string', 'group' => 'billing']
            );
        }

        // Simpan nomor WA notif
        if ($request->filled('qris_wa_notify')) {
            Setting::updateOrCreate(
                ['key' => 'billing_qris_wa_notify'],
                ['value' => $request->qris_wa_notify, 'type' => 'string', 'group' => 'billing']
            );
        }

        return redirect()->back()->with('success', 'Pengaturan QRIS berhasil disimpan.');
    }

    /**
     * Rifal mengkonfirmasi pembayaran QRIS → tambah saldo, ubah status transaksi ke success.
     */
    public function confirmQrisPayment(Request $request, Transaction $transaction)
    {
        if (!Auth::user()->isBillingManager()) {
            abort(403);
        }

        if ($transaction->status !== 'pending' || $transaction->payment_channel !== 'qris') {
            return redirect()->back()->with('error', 'Transaksi tidak valid atau bukan QRIS pending.');
        }

        DB::transaction(function () use ($transaction) {
            $setting = Setting::firstOrCreate(
                ['key' => 'app_balance'],
                ['value' => 0, 'type' => 'number', 'group' => 'billing']
            );

            $balanceBefore = (float) $setting->value;
            $balanceAfter  = $balanceBefore + $transaction->amount;

            // Tambah saldo aplikasi
            $setting->update(['value' => $balanceAfter]);

            // Update transaksi jadi SUCCESS dan perbaiki balance_after
            $transaction->update([
                'status'         => 'success',
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
            ]);
        });

        return redirect()->back()->with('success',
            'Pembayaran QRIS dikonfirmasi. Saldo +Rp ' . number_format($transaction->amount, 0, ',', '.') . ' berhasil ditambahkan.');
    }
}
