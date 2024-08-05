<?php

namespace App\Services\PrivacySettings;

use App\Models\PrivacySettings;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

trait checkingSettings
{
    protected function getSettings(int $user_id): PrivacySettings
    {
        return PrivacySettings::where('user_id', $user_id)->first();
    }

    protected function checkSubscribe(int $user_id)
    {
        $result = Subscription::where('user_id', $user_id)
            ->where('follower_id', Auth::id())
            ->first();

        return (bool)$result;
    }

    protected function checkSubscription(int $user_id)
    {
        $result = Subscription::where('user_id', Auth::id())
            ->where('follower_id', $user_id)
            ->first();

        return (bool)$result;
    }
}
