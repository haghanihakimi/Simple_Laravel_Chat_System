<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGroupMember extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function chat_groups () {
        return $this->belongsTo(ChatGroup::class)
            ->get();
    }
}
