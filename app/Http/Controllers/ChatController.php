<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\CreatePersonalChatRequest;
use App\Http\Requests\Chat\DeleteMessageRequest;
use App\Http\Requests\Chat\DeletePersonalChatRequest;
use App\Http\Requests\Chat\GetChatUsersRequest;
use App\Http\Requests\Chat\GetMessagesRequest;
use App\Http\Requests\Chat\SendMessageRequest;
use App\Http\Requests\Chat\UpdateMessageRequest;
use App\Http\Resources\ChatUserDTOResource;
use App\Http\Resources\Messages\MessageResource;
use App\Http\Resources\Messages\PaginatedMessagesResource;
use App\Http\Resources\PreviewChatDTO\PreviewChatDTOResource;
use App\Models\dto\ChatUserDTO;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    protected ChatService $service;
    public function __construct(ChatService $service)
    {
        $this->service = $service;
    }

    public function getMessages(GetMessagesRequest $request)
    {
        $request = $request->validated();

        $result = $this->service->getMessages($request);

        if (!$result) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return new PaginatedMessagesResource($result);
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

    public function getChatUsers(GetChatUsersRequest $request)
    {
        $request = $request->validated();

        $result = $this->service->getChatUsers($request);

        if (!$result) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return ChatUserDTOResource::collection($result);
    }

    public function getChats()
    {
        $result = $this->service->getPersonalChats();

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
