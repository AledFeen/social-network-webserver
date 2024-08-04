<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedUser extends Model
{
    protected $guarded = false;
    use HasFactory;

    public function blockedUser()
    {
        return $this->belongsTo(User::class, 'blocked_id');
    }
}
