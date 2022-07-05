<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndividualMessage extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeMessage ($query, $target){
        return IndividualMessage::where(function ($query) use ($target) {
            $query->where('sender_id', auth()->user()->id)->where('receiver_id', $target->id);

        })->orWhere(function ($query) use ($target) {
            $query->where('sender_id', $target->id)->where('receiver_id', auth()->user()->id);
        });
    }
}
