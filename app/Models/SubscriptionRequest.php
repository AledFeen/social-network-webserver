<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionRequest extends Model
{
    use HasFactory;

    protected $guarded = false;

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }
}
