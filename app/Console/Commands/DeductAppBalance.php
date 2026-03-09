<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DeductBalanceJob;

class DeductAppBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deduct-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger the balance deduction job';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DeductBalanceJob::dispatchSync();
        $this->info("DeductBalanceJob executed successfully.");
    }
}
