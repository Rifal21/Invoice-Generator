<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Jobs\ExpirePendingTopups;

Schedule::command('recap:daily')->dailyAt('23:00');
Schedule::command('app:deduct-balance')->everyMinute();

// Tandai topup pending > 1 jam sebagai expired
Schedule::job(new ExpirePendingTopups)->everyFiveMinutes();
