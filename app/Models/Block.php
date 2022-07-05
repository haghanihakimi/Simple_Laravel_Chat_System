<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function blocks () {
        return $this->hasManyThrough(User::class, Block::class, 'blocker_user_id',  'id', 'blocked_user_id');
    }

    public function scopeCheckBlocks ($query, $target) {
        return Block::with('blocks')
        ->select('blocker_user_id', 'blocked_user_id')
        ->where('blocked_user_id', $target)
        ->where('blocker_user_id', auth()->user()->id)
        ->orWhere('blocked_user_id', auth()->user()->id)
        ->where('blocker_user_id', $target)
        ->exists();
    }
}
