<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrivacySettings\UpdatePrivacySettingsRequest;
use App\Services\PrivacySettingsService;

class PrivacySettingsController extends Controller
{
    protected $service;

    public function __construct(PrivacySettingsService $service)
    {
        $this->service = $service;
    }

    public function updateSettings(UpdatePrivacySettingsRequest $request)
    {
        $request = $request->validated();

        $result = $this->service->update($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }
}
