<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SuspendedReactivationJob;

class SuspendedAccountTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reactivation:account';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for any account which has SUSPENDED status and is updated last 30 days.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        SuspendedReactivationJob::dispatch();
    }
}
