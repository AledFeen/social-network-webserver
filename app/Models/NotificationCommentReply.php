<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationCommentReply extends Model
{
    use HasFactory;
    protected $guarded = false;

    public function comment()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
}
