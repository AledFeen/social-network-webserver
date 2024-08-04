<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlockedUser\BlockedUserRequest;
use App\Http\Requests\UserDTO\UserDTOResource;
use App\Services\BlockingService;

class BlacklistController extends Controller
{
    protected $service;
    public function __construct(BlockingService $service)
    {
        $this->service = $service;
    }

    public function getBlockedUsers()
    {
        $blockedUsers = $this->service->getBlockedUsers();

        return UserDTOResource::collection($blockedUsers);
    }

    public function block(BlockedUserRequest $request)
    {
        $request = $request->validated();
        $result = $this->service->blockUser($request);
        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function unblock(BlockedUserRequest $request)
    {
        $request = $request->validated();
        $result = $this->service->unblockUser($request);
        return response()->json(['success' => $result], $result ? 200 : 400);
    }

}
