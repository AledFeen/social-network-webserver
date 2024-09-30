<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $guarded = false;
    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_chat_links', 'chat_id', 'user_id');
    }
}
