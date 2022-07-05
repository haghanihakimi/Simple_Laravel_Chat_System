<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UnattendedContactRequestsJob;

class UnattendedContactRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contactRequests:unattended';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command finds that one PENDING contact requests which is unattended/ignored 14 or more than days and removes it.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        UnattendedContactRequestsJob::dispatch();
    }
}
