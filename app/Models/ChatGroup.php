<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGroup extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function chat_group_members () {
        return $this->hasMany(ChatGroupMember::class)
            ->get();
    }
}
