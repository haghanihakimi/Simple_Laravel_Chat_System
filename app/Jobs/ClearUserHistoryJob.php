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
use App\Models\IndividualConversation;
use App\Models\IndividualMessage;
use App\Models\Notification as Notifications;
use Carbon\Carbon;

class ClearUserHistoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = User::withTrashed()->where('deleted_at', '<=', now()->subDays(7))
        ->where('status', '!=', 'suspended')
        ->orWhereNull('status')
        ->where('is_restorable', true)->get();
        
        if (!empty($users)) {
            foreach ($users as $user) {
                $this->clearFriendRequests($user);
                $this->clearMessagesHistory($user);
                $this->clearConversationsHistory($user);
                $this->clearNotificationsHistory($user);
                
                Redis::del('profiles:username:'.$user->uid);
    
                $user->is_restorable = false;
                $user->save();
            }
        }
    }

    private function clearFriendRequests ($user) {
        $contacts = Contact::where('first_user', $user->id)
        ->orWhere('second_user', $user->id)
        ->get();

        if (!empty($contacts) || count($contacts) > 0) {
            foreach ($contacts as $contact) {
                Redis::del('contact.request.user.'.$contact->first_user.'.target.'.$contact->second_user);   
                Redis::del('contact.request.user.'.$contact->second_user.'.target.'.$contact->first_user);
                
                $contact->delete();
            }
        }
    }

    private function clearMessagesHistory ($user) {
        $messages = IndividualMessage::where('sender_id', $user->id)
        ->orWhere('receiver_id', $user->id)->get();

        if (!empty($messages) || count($messages) > 0) {
            foreach ($messages as $message) {
                Redis::del('message:id:'.$message->id);
                $message->delete();
            }
        }
    }

    private function clearConversationsHistory ($user) {
        $conversations = IndividualConversation::where('creator_id', $user->id)
        ->orWhere('host_id', $user->id)->get();

        if (!empty($conversations) || count($conversations) > 0) {
            foreach ($conversations as $conversation) {
                Redis::del('message:id:'.$conversation->id);
                $conversation->delete();
            }
        }
    }

    //notifiable_id 
    private function clearNotificationsHistory ($user) {
        $notifications = Notifications::where('notifiable_id', $user->id)
        ->get();

        if (!empty($notifications) || count($notifications) > 0) {
            foreach ($notifications as $notification) {
                $notification->delete();
            }
        }
    }
}
