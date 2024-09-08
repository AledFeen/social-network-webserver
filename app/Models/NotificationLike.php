<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLike extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function like()
    {
        return $this->belongsTo(PostLike::class, 'like_id');
    }
}
