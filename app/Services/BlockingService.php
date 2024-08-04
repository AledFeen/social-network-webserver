<?php

namespace App\Services;

use App\Models\BlockedUser;
use App\Models\dto\UserDTO;
use Illuminate\Support\Facades\Auth;

class BlockingService
{
    public function getBlockedUsers()
    {
        return BlockedUser::where('user_id', Auth::id())
            ->with('blockedUser.account')
            ->get()
            ->map(function ($blocked) {
                return new UserDTO($blocked->blockedUser->id, $blocked->blockedUser->name, $blocked->blockedUser->account->image);
            });
    }

    public function blockUser(array $request)
    {
        $created = BlockedUser::create([
           'user_id' => Auth::id(),
           'blocked_id' => $request['user_id']
        ]);

        return (bool) $created;
    }

    public function unblockUser(array $request)
    {
        $deleted = BlockedUser::where('user_id', Auth::id())
            ->where('blocked_id', $request['user_id'])
            ->delete();

        return (bool)$deleted;
    }
}
