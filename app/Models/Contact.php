<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Contact extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function scopeContacts ($query){
        $all_contacts = [];

        $contacts = Contact::where('user_id', auth()->user()->id)
        ->where('status', 'accepted')
        ->orWhere('target_id', auth()->user()->id)
        ->where('status', 'accepted')
        ->get();

        foreach ($contacts as $contact) {
            $all_contacts[] = User::where('id', $contact->user_id)
            ->orWhere('id', $contact->target_id)
            ->select('id', 'fname', 'sname', 'avatar')->get();
        }

        return $all_contacts;
    }

    public function scopePendingContacts ($query){
        $all_contacts = [];

        $contacts = Contact::where('user_id', auth()->user()->id)
        ->where('status', 'pending')
        ->orWhere('target_id', auth()->user()->id)
        ->where('status', 'pending')
        ->get();

        foreach ($contacts as $contact) {
            $all_contacts[] = User::where('id', $contact->user_id)
            ->orWhere('id', $contact->target_id)
            ->select('id', 'fname', 'sname', 'avatar')->get();
        }

        return $all_contacts;
    }

    public function contactsUser () {
        return $this->hasManyThrough(Contact::class, User::class, 'id', 'user_id', 'target_id');
    }

    public function contactsTarget () {
        return $this->hasMany(Contact::class, 'target_id', 'id', 'user_id');
    }
}
