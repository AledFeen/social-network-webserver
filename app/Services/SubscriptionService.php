<?php

namespace App\Services;

use App\Models\dto\UserDTO;
use App\Models\User;

class SubscriptionService
{
    public function subscribers($request)
    {
        $user = $this->findUser($request);

        $followers = $user->followers()->with('follower.account')->get()->map(function ($subscription) {
            return new UserDTO($subscription->follower->id, $subscription->follower->name, $subscription->follower->account->image);
        });

        return $followers;
    }

    public function subscriptions($request)
    {
        $user = $this->findUser($request);

        $following = $user->following()->with('user.account')->get()->map(function ($subscription) {
            return new UserDTO($subscription->follower->id, $subscription->follower->name, $subscription->follower->account->image);
        });

        return $following;
    }

    protected function findUser($request)
    {
        $user_id = $request['user_id'];
        return User::find($user_id);
    }

}
