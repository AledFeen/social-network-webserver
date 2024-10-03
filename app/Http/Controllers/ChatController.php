<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\CreatePersonalChatRequest;
use App\Http\Requests\Chat\DeleteMessageRequest;
use App\Http\Requests\Chat\DeletePersonalChatRequest;
use App\Http\Requests\Chat\SendMessageRequest;
use App\Http\Requests\Chat\UpdateMessageRequest;
use App\Http\Resources\PreviewChatDTO\PreviewChatDTOResource;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    protected ChatService $service;
    public function __construct(ChatService $service)
    {
        $this->service = $service;
    }

    public function sendMessage(SendMessageRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->sendMessage($request);

        return response()->json(['success' => $result], $result ? 201 : 400);
    }

    public function updateMessageText(UpdateMessageRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->updateTextMessage($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function deleteMessage(DeleteMessageRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->deleteMessage($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function getChats()
    {
        $result = $this->service->getChats();

        return PreviewChatDTOResource::collection($result);
    }

    public function createPersonalChat(CreatePersonalChatRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->createPersonalChat($request);

        return response()->json(['success' => $result], $result ? 201 : 400);
    }

    public function deletePersonalChat(DeletePersonalChatRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->deletePersonalChat($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }


}
