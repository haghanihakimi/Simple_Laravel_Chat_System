<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ClearUserHistoryJob;

class DeletedUsersHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dataEraser:usersData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "This command runs every 1 hour and check if any soft deleted 
    users' account is deleted for 7 or more than 7 days. All users' data will be erased permantently, 
    except some specific information.";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ClearUserHistoryJob::dispatch();
    }
}
