<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Registrations;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class RegistrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = $this->url_generator($this->user);

        $data = [
            "greeting" => "Hi ".$this->user->fname,
            "header" => "Thank you for registering on <strong>Dreams</strong>.<br/>Please click on the below button to verify you account.",
            "button" => "Verify Your Account",
            "link" => $url,
            "footer" => ""
        ];
        $message = "Activation link has been sent to your email address.";
        
        $notify = Notification::send($this->user, new Registrations($this->user, $message, "verification", $data, null));
    }

    private function url_generator ($target)
    {
        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 10)),
            [
                'id' => $target->getKey(),
                'hash' => sha1($target->getEmailForVerification())
            ]
        );

        return $url;
    }
}
