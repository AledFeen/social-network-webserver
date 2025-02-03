<?php

namespace App\Services;

use App\Models\BlockedUser;
use App\Models\dto\UserDTO;
use App\Models\Subscription;
use App\Services\Paginate\PaginatedResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BlockingService
{
    public function getBlockedUsers(array $request): PaginatedResponse
    {
         BlockedUser::where('user_id', Auth::id())
            ->with('blockedUser.account')
            ->paginate(10, ['*'], 'page', $request['page_id'])
            ->map(function ($blocked) {
                return new UserDTO($blocked->blockedUser->id, $blocked->blockedUser->name, $blocked->blockedUser->account->image);
            });

        $paginatedUsers = BlockedUser::where('user_id', Auth::id())
            ->with('blockedUser.account')
            ->paginate(10, ['*'], 'page', $request['page_id']);

        $data = $paginatedUsers->map(function ($subscription) {
            return new UserDTO(
                $subscription->blockedUser->id,
                $subscription->blockedUser->name,
                $subscription->blockedUser->account->image
            );
        });

        return new PaginatedResponse(
            $data,
            $paginatedUsers->currentPage(),
            $paginatedUsers->lastPage(),
            $paginatedUsers->total()
        );
    }

    public function blockUser(array $request)
    {
        if (Auth::id() != $request['user_id']) {
            DB::beginTransaction();
            try {

                Subscription::where('user_id', Auth::id())
                    ->where('follower_id', $request['user_id'])
                    ->delete();

                Subscription::where('user_id', $request['user_id'])
                    ->where('follower_id', Auth::id())
                    ->delete();

                $created = BlockedUser::create([
                    'user_id' => Auth::id(),
                    'blocked_id' => $request['user_id']
                ]);

                DB::commit();
                return (bool)$created;
            } catch
            (\Exception $e) {
                DB::rollBack();
                logger($e);
                return false;
            }

        } else return false;
    }

    public function unblockUser(array $request)
    {
        $deleted = BlockedUser::where('user_id', Auth::id())
            ->where('blocked_id', $request['user_id'])
            ->delete();

        return (bool)$deleted;
    }
}
