<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
Use App\Jobs\ProfilesUpdaterJob;

class ProfileDBUpdater extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:profileupdater';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        dispatch ( new ProfilesUpdaterJob() )->delay(now()->addSeconds(3));
    }
}
