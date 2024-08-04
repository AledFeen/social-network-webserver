<?php

namespace App\Services\blacklist;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait CheckBlacklist
{
    public function blockedBy()
    {
        return User::where('id', Auth::id())->first()->blockedBy()->pluck('user_id');
    }
}
