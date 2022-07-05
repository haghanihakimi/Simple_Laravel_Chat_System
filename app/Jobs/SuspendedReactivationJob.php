<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AccountReactivation;
use App\Models\User;
use Carbon\Carbon;

class SuspendedReactivationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
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
        $users = User::where('status', 'suspended')
        ->where('deleted_at', '<=', now()->subDays(30))
        ->get();

        foreach ($users as $user) {
            $user->update([
                'status' => null,
                'is_restorable' => null
            ]);
            $user->save();
            $user->restore();

            $this->message($user);

        }
    }

    private function message ($user) {
        $data = [
            "greeting" => "Hello dear ".$user->fname.",",
            "header" => "Your account was suspended for last 30 days.<br/>Now suspension is over you can login to your account.<br/>Please read our terms and policies to learn more about how to avoid any more suspensions in the future!<br/>Thank you for your patience...",
            "button" => "Terms & Policies",
            "link" => '/terms/policies',
            "footer" => ""
        ];
        $message = $user->fname."'s suspended account has been reactivated.";
        
        Notification::send($user, new AccountReactivation($user, $message, "verification", $data, null));
    }
}
