<?php

namespace App\Services;

use App\Models\BlockedUser;
use App\Models\dto\UserDTO;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        DB::beginTransaction();
        try {
            Subscription::where('user_id', Auth::id())
                ->where('follower_id', $request['user_id'])
                ->delete();

            $created = BlockedUser::create([
                'user_id' => Auth::id(),
                'blocked_id' => $request['user_id']
            ]);

            return (bool) $created;
        }
        catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return false;
        }
    }

    public function unblockUser(array $request)
    {
        $deleted = BlockedUser::where('user_id', Auth::id())
            ->where('blocked_id', $request['user_id'])
            ->delete();

        return (bool)$deleted;
    }
}
