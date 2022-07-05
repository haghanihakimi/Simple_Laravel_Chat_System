<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ContactRequest AS MailContactRequest;
use App\Models\Contact;

class ContactRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $sender;
    protected $target;

    public function __construct($target, $sender)
    {
        $this->target = $target;
        $this->sender = $sender;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (Redis::exists('contact.request.user.'.$this->sender->uid.'.target.'.$this->target->uid)) {            
            $data = [
                "greeting" => "Hi ".$this->target->fname,
                "header" => "You have new contact request. Please open your profile and view you recent contact request(s)",
                "button" => $this->target->fname." Profile",
                "link" => route('feeds'),
                "footer" => ""
            ];
            $message = "<a href='#' target='_self'>".$this->sender->fname." ".$this->sender->sname." sent you a contact request!</a>";
            
            Notification::send($this->target, new MailContactRequest($this->sender, $message, "contact_request", $data, null));         
        }
    }
}
