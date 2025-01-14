<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function checkAuth(): array
    {
        if (Auth::id()) return ['token' => true];
        return ['token' => false];
    }

    public function user(): UserResource
    {
       return new UserResource(Auth::user());
    }
}
