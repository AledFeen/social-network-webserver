<?php

namespace App\Services;

use App\Models\PrivacySettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivacySettingsService
{
    public function update($request):bool {
        $updated = PrivacySettings::where('user_id', Auth::id())->update([
            'account_type' => $request['account_type'],
            'who_can_comment' => $request['who_can_comment'],
            'who_can_repost' => $request['who_can_repost'],
            'who_can_message' => $request['who_can_message']
        ]);

        return (bool) $updated;
    }

}
