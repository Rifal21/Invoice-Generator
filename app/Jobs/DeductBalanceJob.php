<?php

namespace App\Jobs;

use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DeductBalanceJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("--- [Job] Billing Deduction Process Started ---");

        // Check billing status
        $status = Setting::where('key', 'app_billing_status')->first()?->value ?? 'active';
        if ($status === 'disabled') {
            Log::info("Status: DISABLED. No deduction.");
            return;
        }

        // Get balance and rate
        $balanceSetting = Setting::where('key', 'app_balance')->first();
        $rateSetting = Setting::where('key', 'app_billing_rate_per_minute')->first();

        if (!$balanceSetting || !$rateSetting) {
            Log::error("Error: Missing settings (balance or rate).");
            return;
        }

        $currentBalance = (float) $balanceSetting->value;
        $ratePerMinute = (float) $rateSetting->value;

        // Get timing
        $lastUpdatedSetting = Setting::where('key', 'app_billing_last_updated_at')->first();
        if (!$lastUpdatedSetting) {
            Setting::create([
                'key' => 'app_billing_last_updated_at',
                'value' => now()->toDateTimeString(),
                'type' => 'datetime',
                'group' => 'billing'
            ]);
            return;
        }

        if ($currentBalance <= 0) {
            // Reset timestamp to current to prevent massive debt when user finally tops up
            $lastUpdatedSetting->update(['value' => now()->toDateTimeString()]);
            Log::info("Status: Balance empty. Timestamp reset.");
            return;
        }

        if ($ratePerMinute <= 0) {
            Log::info("Status: Rate is 0.");
            return;
        }

        $lastUpdated = Carbon::parse($lastUpdatedSetting->value);
        $now = now();
        
        // Use diffInMinutes directly
        $minutesElapsed = $lastUpdated->diffInMinutes($now, false);

        if ($minutesElapsed < 1) {
            Log::info("Minutes passed: {$minutesElapsed}. Waiting for full minute.");
            return;
        }

        // Calculate
        $deduction = $minutesElapsed * $ratePerMinute;
        $newBalance = max(0, $currentBalance - $deduction);
        $newLastUpdated = $lastUpdated->copy()->addMinutes($minutesElapsed);

        Log::info("Deducting: Rp " . number_format($deduction, 2) . " for {$minutesElapsed} minutes.");

        // Update
        DB::transaction(function () use ($balanceSetting, $lastUpdatedSetting, $newBalance, $newLastUpdated) {
            $balanceSetting->update(['value' => $newBalance]);
            $lastUpdatedSetting->update(['value' => $newLastUpdated->toDateTimeString()]);
        });

        Log::info("Success! New balance: Rp " . number_format($newBalance, 2));
    }
}
