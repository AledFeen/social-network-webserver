<?php

namespace App\Http\Controllers;

use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected ChatService $service;
    public function __construct(ChatService $service)
    {
        $this->service = $service;
    }

}
