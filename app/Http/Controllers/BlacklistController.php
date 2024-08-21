<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlockedUser\BlockedUserRequest;
use App\Http\Resources\UserDTO\UserDTOResource;
use App\Services\BlockingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BlacklistController extends Controller
{
    protected BlockingService $service;
    public function __construct(BlockingService $service)
    {
        $this->service = $service;
    }

    public function getBlockedUsers(): AnonymousResourceCollection
    {
        $blockedUsers = $this->service->getBlockedUsers();

        return UserDTOResource::collection($blockedUsers);
    }

    public function block(BlockedUserRequest $request): JsonResponse
    {
        $request = $request->validated();
        $result = $this->service->blockUser($request);
        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function unblock(BlockedUserRequest $request): JsonResponse
    {
        $request = $request->validated();
        $result = $this->service->unblockUser($request);
        return response()->json(['success' => $result], $result ? 200 : 400);
    }

}
