<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Transaction;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class PaymentCallbackController extends Controller
{
    public function midtransCallback(Request $request)
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');

        // Log raw payload untuk debugging
        Log::info('[Midtrans Callback] Raw payload:', $request->all());

        // Ambil data dari request body (JSON atau form)
        $payload = $request->all();

        // Verifikasi signature key dari Midtrans
        if (!empty($payload['signature_key']) && !empty($payload['order_id'])) {
            $orderId        = $payload['order_id'];
            $statusCode     = $payload['status_code'];
            $grossAmount    = $payload['gross_amount'];
            $serverKey      = config('services.midtrans.server_key');

            $expectedSig = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            if ($payload['signature_key'] !== $expectedSig) {
                Log::warning('[Midtrans Callback] Signature mismatch! Order: ' . $orderId);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $transactionStatus = $payload['transaction_status'];
            $type              = $payload['payment_type'];
            $fraud             = $payload['fraud_status'] ?? null;
        } else {
            // Fallback: pakai library Notification
            try {
                $notif = new Notification();
                $transactionStatus = $notif->transaction_status;
                $type              = $notif->payment_type;
                $orderId           = $notif->order_id;
                $fraud             = $notif->fraud_status;
            } catch (\Exception $e) {
                Log::error('[Midtrans Callback] Notification error: ' . $e->getMessage());
                return response()->json(['message' => 'Invalid notification'], 400);
            }
        }

        Log::info("[Midtrans Callback] Order: {$orderId}, Status: {$transactionStatus}, Type: {$type}");

        $localTransaction = Transaction::where('reference_id', $orderId)->first();

        if (!$localTransaction) {
            Log::warning("[Midtrans Callback] Transaction not found: {$orderId}");
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        if ($localTransaction->status !== 'pending') {
            Log::info("[Midtrans Callback] Already processed: {$orderId} (status: {$localTransaction->status})");
            return response()->json(['message' => 'Transaction already processed'], 200);
        }

        DB::beginTransaction();
        try {
            if ($transactionStatus == 'capture') {
                // Credit card capture
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $localTransaction->update(['status' => 'pending']);
                    } else {
                        $this->finalizeTopup($localTransaction, $type);
                    }
                }
            } else if ($transactionStatus == 'settlement') {
                // QRIS, GoPay, Transfer VA, dll — semua tipe yang settled
                $this->finalizeTopup($localTransaction, $type);
            } else if ($transactionStatus == 'pending') {
                // Menunggu pembayaran (QRIS belum di-scan, VA belum ditransfer)
                $localTransaction->update(['status' => 'pending']);
            } else if ($transactionStatus == 'deny') {
                $localTransaction->update(['status' => 'denied']);
            } else if ($transactionStatus == 'expire') {
                $localTransaction->update(['status' => 'expired']);
            } else if ($transactionStatus == 'cancel') {
                $localTransaction->update(['status' => 'cancelled']);
            }

            $localTransaction->update(['payment_channel' => $type]);

            DB::commit();
            Log::info("[Midtrans Callback] Success processed: {$orderId}");
            return response()->json(['message' => 'Success', 'status' => $transactionStatus, 'type' => $type], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[Midtrans Callback] Database error: " . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    protected function finalizeTopup($transaction, string $paymentType = '')
    {
        $transaction->update(['status' => 'success']);

        $setting = Setting::firstOrCreate(
            ['key' => 'app_balance'],
            ['value' => 0, 'type' => 'number', 'group' => 'billing']
        );

        $balanceBefore = (float) $setting->value;
        $balanceAfter = $balanceBefore + $transaction->amount;

        $setting->update(['value' => $balanceAfter]);

        $channelLabel = match(strtolower($paymentType)) {
            'qris'       => 'QRIS',
            'gopay'      => 'GoPay / QRIS',
            'shopeepay'  => 'ShopeePay',
            'dana'       => 'DANA',
            'ovo'        => 'OVO',
            'credit_card'=> 'Kartu Kredit',
            'bank_transfer' => 'Transfer Bank',
            'echannel'   => 'Mandiri Bill',
            'bca_klikpay'=> 'BCA KlikPay',
            default      => strtoupper($paymentType) ?: 'Midtrans',
        };

        $transaction->update([
            'balance_before'   => $balanceBefore,
            'balance_after'    => $balanceAfter,
            'payment_channel'  => $paymentType,
            'description'      => $transaction->description . " | Dikonfirmasi via {$channelLabel}.",
        ]);

        // Reset billing timestamp
        Setting::updateOrCreate(
            ['key' => 'app_billing_last_updated_at'],
            ['value' => now()->toDateTimeString(), 'type' => 'datetime', 'group' => 'billing']
        );
    }

    public function checkStatus(string $orderId)
    {
        $transaction = Transaction::where('reference_id', $orderId)->first();

        if (!$transaction) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json([
            'status'          => $transaction->status,
            'payment_channel' => $transaction->payment_channel,
            'amount'          => $transaction->amount,
            'balance_after'   => $transaction->balance_after,
        ]);
    }
}
