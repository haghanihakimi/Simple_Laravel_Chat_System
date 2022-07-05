<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use App\Models\Contact;
use App\Models\DeletedAccount;
use Carbon\Carbon;

class UnattendedContactRequestsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($trigger)
    {
        // 
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $contacts = Contact::where('status', 'pending')
        ->where('created_at', '<=', now()->subDays(14))
        ->get();

        foreach ($contacts as $contact) {
            if (Redis::exists('contact.request.user.'.$contact->first_user.'.target.'.$contact->second_user)) {
                Redis::del('contact.request.user.'.$contact->first_user.'.target.'.$contact->second_user);
            } 
            if (Redis::exists('contact.request.user.'.$contact->second_user.'.target.'.$contact->first_user)) {
                Redis::del('contact.request.user.'.$contact->second_user.'.target.'.$contact->first_user);
            }
            $contact->delete();
        }
    }
}
