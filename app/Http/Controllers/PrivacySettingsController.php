<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrivacySettings\UpdatePrivacySettingsRequest;
use App\Http\Resources\PrivacySettingsResource;
use App\Services\PrivacySettings\PrivacySettingsService;
use Illuminate\Http\JsonResponse;

class PrivacySettingsController extends Controller
{
    protected PrivacySettingsService $service;

    public function __construct(PrivacySettingsService $service)
    {
        $this->service = $service;
    }

    public function getSettings(): PrivacySettingsResource
    {
        $data = $this->service->get();

        return new PrivacySettingsResource($data);
    }

    public function updateSettings(UpdatePrivacySettingsRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->update($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }
}
