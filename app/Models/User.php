<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\CanResetPassword;
use App\Jobs\PasswordResetJob;
//use Laravel\Scout\Searchable;

class User extends Authenticatable implements MustVerifyEmail, CanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable, SoftDeletes;// Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];
    
    // protected $fillable = [
        //     'uid',
        //     'fname',
        //     'sname',
        //     'email',
        //     'password',
        //     'phone',
        //     'gender'
    // ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // public function sendPasswordResetNotification($token)
    // {
    //     //dispactches the job to the queue passing it this User object
    //     PasswordResetJob::dispatch($this,$token);
    // }

    public function blocks () {
        return $this->hasMany(Block::class, 'blocked_user_id', 'blocker_user_id');
    }
    
    public function searchPeople () {
        return $this->belongsTo(User::class);
    }

    public function sentContactRequests (){
        return $this->belongsToMany(User::class, 'contacts', 'first_user', 'second_user')
            ->withPivot(['status', 'created_at', 'updated_at'])
            ->wherePivot('status', 'accepted')
            ->get();
    }

    public function receivedContactRequests (){
        return $this->belongsToMany(User::class, 'contacts', 'second_user', 'first_user')
        ->withPivot(['status', 'created_at', 'updated_at'])
        ->wherePivot('status', 'accepted')
        ->get();
    }

    public function contacts () {
        $received = $this->receivedContactRequests();
        $sent = $this->sentContactRequests();

        return $received->merge($sent);
    }

    public function receivedPendingRequests () {
        return $this->belongsToMany(User::class, 'contacts', 'second_user', 'first_user')
        ->withPivot(['status', 'created_at', 'updated_at'])
        ->wherePivot('status', 'pending')
        ->get();
    }

    public function sentPendingRequests () {
        return $this->belongsToMany(User::class, 'contacts', 'first_user', 'second_user')
                ->withPivot(['status', 'created_at', 'updated_at'])
                ->wherePivot('status', 'pending')
                ->get();
    }

    public function hasBlocked () {
        return $this->belongsToMany(User::class, 'blocks', 'blocker_user_id', 'blocked_user_id')
                ->withTimestamps()
                ->get();
    }

    public function beingBlocked () {
        return $this->belongsToMany(User::class, 'blocks', 'blocked_user_id', 'blocker_user_id')
                ->withTimestamps()
                ->get();
    }

    public function blockList () {
        $hasBlocked = $this->hasBlocked();
        $beingBlocked = $this->beingBlocked();

        return $beingBlocked->merge($hasBlocked);
    }

    public function sentRequestRejected () {
        return $this->belongsToMany(User::class, 'contacts', 'first_user', 'second_user')
            ->wherePivot('is_spam', true)
            ->get();
    }

    public function activeSpamMark () {
        return $this->belongsToMany(User::class, 'contacts', 'second_user', 'first_user')
            ->withPivot(['is_spam', 'status'])
            ->wherePivot('is_spam', false)
            ->wherePivot('status', 'pending')
            ->withTimestamps()
            ->get();
    }

    public function spammedUsers () {
        return $this->belongsToMany(User::class, 'contacts', 'second_user', 'first_user')
            ->withPivot('is_spam')
            ->wherePivot('is_spam', true)
            ->withTimestamps()
            ->get();
    }

    public function beingSpammed () {
        return $this->belongsToMany(User::class, 'contacts', 'first_user', 'second_user')
            ->withPivot('is_spam')
            ->wherePivot('is_spam', true)
            ->withTimestamps()
            ->get();
    }

    public function checkCancellable ($target) {
        $pendings = $this->sentPendingRequests();
        $check = false;

        if (!empty($pendings) || count($pendings) > 0) {
            foreach ($pendings as $pending) {
                if ($pending->id === $target->id) {
                    $check = true;
                }
            }
        }

        return $check;
    }

    public function checkRejectable ($target) {
        $pendings = $this->receivedPendingRequests();
        $check = false;

        if (!empty($pendings) || count($pendings) > 0) {
            foreach ($pendings as $pending) {
                if ($pending->id === $target->id){
                    $check = true;
                }
            }
        }

        return $check;
    }

    public function checkAcceptable ($target) {
        $pendings = $this->receivedPendingRequests();
        $check = false;

        if (!empty($pendings) || count($pendings) > 0) {
            foreach ($pendings as $pending) {
                if ($pending->id === $target->id) {
                    $check = true;
                }
            }
        }

        return $check;
    }

    public function checkMessageable ($target) {
        $contacts = $this->contacts();
        $blocks = $this->blockList();
        $check = false;

        if (count($contacts) > 0) {
            foreach ($contacts as $contact) {
                if ($contact->id === $target->id || !$target->is_locked) {
                    $check = true;
                }
            }
        }
        if (!$target->is_locked) {
            $check = true;
        }
        if (count($blocks) > 0) {
            foreach ($blocks as $block) {
                if ($block->id === $target->id) {
                    $check = false;
                }
            }
        }

        return $check;
    }

    public function checkAddable ($target) {
        $check = false;
        $blocks = $this->blockList();
        $contacts = $this->contacts();

        if ( !$this->checkCancellable($target) && !$this->checkRejectable($target) && !$this->checkAcceptable($target) && !$this->checkBeingSpammed($target) ) {
            $check = true;
            if (count($blocks) > 0) {
                foreach ($blocks as $block) {
                    if ($block->id === $target->id) {
                        $check = false;
                    }
                }
            }
            if (count($contacts) > 0) {
                foreach ($contacts as $contact) {
                    if ($contact->id === $target->id) {
                        $check = false;
                    }
                }
            }
        }

        return $check;
    }

    public function checkContactRemovable ($target) {
        $contacts = $this->contacts();
        $check = false;

        if (count($contacts) > 0) {
            foreach ($contacts as $contact) {
                if ($contact->id === $target->id) {
                    $check = true;
                }
            }
        }

        return $check;
    }

    public function checkSpamMarkable ($target) {
        $spams = $this->activeSpamMark();
        $check = false;

        if (count($spams) > 0) {
            foreach ($spams as $spam) {
                if ($spam->id === $target->id) {
                    $check = true;
                }
            }
        }

        return $check;
    }

    public function checkBeingSpammed ($target) {
        $spammed = $this->beingSpammed();
        $check = false;

        if (count($spammed) > 0) {
            foreach ($spammed as $spam) {
                if ($spam->id === $target->id) {
                    $check = true;
                }
            }
        }

        return $check;
    }

    public function checkHasSpammed ($target){
        $spammed = $this->spammedUsers();
        $check = false;

        if (count($spammed) > 0) {
            foreach ($spammed as $spam) {
                if ($spam->id === $target->id){
                    $check = true;
                }
            }
        }
        return $check;
    }

    public function checkBeingBlocked ($target) { 
        $blocks = $this->beingBlocked();
        $check = false;
        if (count($blocks) > 0) {
            foreach ($blocks as $block) {
                if ($block->id === $target->id) { 
                    $check = true;
                }
            }
        }
        return $check;
    }

    public function checkHasBlocked ($target) {
        $blocks = $this->hasBlocked();
        $check = false;
        if (count($blocks) > 0) {
            foreach ($blocks as $block) {
                if ($block->id === $target->id) { 
                    $check = true;
                }
            }
        }
        return $check;
    }

    public function checkBlockable ($target) {
        $blocks = $this->hasBlocked();
        $check = true;

        if (count($blocks) > 0) {
            foreach ($blocks as $block) {
                if ($block->id === $target->id) {
                    $check = false;
                }
            }
        }

        return $check;
    }

    public function sentMessage ($target) {
        return $this->belongsToMany(User::class, 'individual_messages', 'sender_id', 'receiver_id')
            ->withPivot(['id', 'public_id', 'created_at', 'updated_at', 'deleted_by'])
            ->wherePivot('receiver_id', $target->id)
            ->limit(100)
            ->get();
    }

    public function receivedMessage ($target) {
        return $this->belongsToMany(User::class, 'individual_messages', 'receiver_id', 'sender_id')
            ->withPivot(['id', 'public_id', 'created_at', 'updated_at', 'deleted_by'])
            ->wherePivot('sender_id', $target->id)
            ->limit(100)
            ->get();
    }

    public function individualConversationCreator () {
        return $this->belongsToMany(User::class, 'individual_conversations', 'creator_id', 'host_id')
        ->withPivot(['public_id', 'created_at', 'updated_at', 'deleted_by'])
        ->wherePivot('deleted_by', '=', null)
        ->orWherePivot('deleted_by', '!=', auth()->user()->id);
    }

    public function individualConversationHost () {
        return $this->belongsToMany(User::class, 'individual_conversations', 'host_id', 'creator_id')
        ->withPivot(['public_id', 'created_at', 'updated_at'])
        ->wherePivot('deleted_by', '=', null)
        ->orWherePivot('deleted_by', '!=', auth()->user()->id);
    }

    public function individualConversations () {
        $creator = $this->individualConversationCreator()->get();
        $host = $this->individualConversationHost()->get();

        return $creator->merge($host);
    }

    public function individualConversationsCheck ($target) {
        $conversations = $this->individualConversations();
        $check = false;

        if (count($conversations) > 0) {
            foreach ($conversations as $conversation){
                if ($conversation->pivot->creator_id === $target->id || $conversation->pivot->host_id === $target->id) {
                    $check = true;
                }
            }
        }

        return $check;
    }
}