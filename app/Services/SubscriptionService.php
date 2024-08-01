<?php

namespace App\Services;

use App\Models\dto\UserDTO;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SubscriptionService
{
    public function subscribe(array $request)
    {
        $user_id = $request['user_id'];
        $follower_id = Auth::id();

        if ($follower_id != $user_id) {
            $created = Subscription::create([
                'user_id' => $user_id,
                'follower_id' => $follower_id
            ]);
        } else {
            $created = false;
        }

        return (bool)$created;
    }

    public function unsubscribe(array $request)
    {
        $deleted = Subscription::where('user_id', $request['user_id'])
            ->where('follower_id', Auth::id())
            ->delete();

        return (bool)$deleted;
    }

    public function deleteSubscriber(array $request)
    {
        $deleted = Subscription::where('user_id', Auth::id())
            ->where('follower_id', $request['user_id'])
            ->delete();

        return (bool)$deleted;
    }

    public function subscribers(array $request)
    {
        $user = $this->findUser($request);

        $followers = $user->followers()->with('follower.account')->get()->map(function ($subscription) {
            return new UserDTO($subscription->follower->id, $subscription->follower->name, $subscription->follower->account->image);
        });

        return $followers;
    }

    public function subscriptions(array $request)
    {
        $user = $this->findUser($request);

        $following = $user->following()->with('user.account')->get()->map(function ($subscription) {
            return new UserDTO($subscription->follower->id, $subscription->follower->name, $subscription->follower->account->image);
        });

        return $following;
    }

    protected function findUser(array $request)
    {
        $user_id = $request['user_id'];
        return User::find($user_id);
    }

}
