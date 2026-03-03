<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExpirePendingTopups implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jalankan job: tandai transaksi topup pending yang sudah > 1 jam sebagai expired.
     */
    public function handle(): void
    {
        $expiredCount = Transaction::query()
            ->where('type', 'topup')
            ->where('status', 'pending')
            ->where('created_at', '<=', now()->subHour())
            ->update(['status' => 'expired']);

        if ($expiredCount > 0) {
            Log::info("[ExpirePendingTopups] {$expiredCount} transaksi pending kedaluwarsa setelah 1 jam.");
        }
    }
}
