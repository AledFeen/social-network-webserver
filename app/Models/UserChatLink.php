<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChatLink extends Model
{
    use HasFactory;
    protected $guarded = false;
    public $timestamps = false;

    public function messages()
    {
        return $this->hasMany(Message::class, 'link_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
