<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeductAppBalance extends Command
{
    protected $signature = 'app:deduct-balance';
    protected $description = 'Deduct the global app balance based on the current minute rate.';

    public function handle()
    {
        $this->info("--- Billing Deduction Process ---");

        // Check billing status
        $status = Setting::where('key', 'app_billing_status')->first()?->value ?? 'active';
        if ($status === 'disabled') {
            $this->warn("Status: DISABLED. No deduction.");
            return;
        }

        // Get balance and rate
        $balanceSetting = Setting::where('key', 'app_balance')->first();
        $rateSetting = Setting::where('key', 'app_billing_rate_per_minute')->first();

        if (!$balanceSetting || !$rateSetting) {
            $this->error("Error: Missing settings (balance or rate).");
            return;
        }

        $currentBalance = (float) $balanceSetting->value;
        $ratePerMinute = (float) $rateSetting->value;

        if ($currentBalance <= 0) {
            $this->warn("Status: Balance empty.");
            return;
        }

        if ($ratePerMinute <= 0) {
            $this->warn("Status: Rate is 0.");
            return;
        }

        // Get timing
        $lastUpdatedSetting = Setting::where('key', 'app_billing_last_updated_at')->first();
        if (!$lastUpdatedSetting) {
            $this->info("Initializing last updated timestamp...");
            Setting::create([
                'key' => 'app_billing_last_updated_at',
                'value' => now()->toDateTimeString(),
                'type' => 'datetime',
                'group' => 'billing'
            ]);
            return;
        }

        $lastUpdated = Carbon::parse($lastUpdatedSetting->value);
        $now = now();
        
        // Use diffInMinutes directly
        $minutesElapsed = $lastUpdated->diffInMinutes($now, false);

        $this->info("Current Time: " . $now->toDateTimeString());
        $this->info("Last Updated: " . $lastUpdated->toDateTimeString());
        $this->info("Minutes Passed: " . $minutesElapsed);

        if ($minutesElapsed < 1) {
            $this->info("Waiting for next full minute...");
            return;
        }

        // Calculate
        $deduction = $minutesElapsed * $ratePerMinute;
        $newBalance = max(0, $currentBalance - $deduction);
        $newLastUpdated = $lastUpdated->copy()->addMinutes($minutesElapsed);

        $this->info("Deducting: Rp " . number_format($deduction, 2));

        // Update
        DB::transaction(function () use ($balanceSetting, $lastUpdatedSetting, $newBalance, $newLastUpdated) {
            $balanceSetting->update(['value' => $newBalance]);
            $lastUpdatedSetting->update(['value' => $newLastUpdated->toDateTimeString()]);
        });

        $this->info("Success! New balance: Rp " . number_format($newBalance, 2));
    }
}
