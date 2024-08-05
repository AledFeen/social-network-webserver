<?php

namespace App\Services\Blacklist;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait checkingBlacklist
{
    public function blockedBy()
    {
        return User::where('id', Auth::id())->first()->blockedBy()->pluck('user_id');
    }
}
