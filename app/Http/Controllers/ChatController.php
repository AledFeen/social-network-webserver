<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\CreatePersonalChatRequest;
use App\Http\Requests\Chat\SendMessageRequest;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected ChatService $service;
    public function __construct(ChatService $service)
    {
        $this->service = $service;
    }

    public function createPersonalChat(CreatePersonalChatRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->createPersonalChat($request);

        return response()->json(['success' => $result], $result ? 201 : 400);
    }

    public function sendMessage(SendMessageRequest $request)
    {
        $request = $request->validated();

        $result = $this->service->sendMessage($request);

        return response()->json(['success' => $result], $result ? 201 : 400);
    }
}
