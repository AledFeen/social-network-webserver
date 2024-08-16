<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\GetAccountRequest;
use App\Http\Requests\Account\UpdateAccountRequest;
use App\Http\Requests\Account\UpdateProfileImageRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{

    protected AccountService $service;

    public function __construct(AccountService $service)
    {
        $this->service = $service;
    }

    public function getMyAccount(): AccountResource
    {
        $data = $this->service->getMy();

        return new AccountResource($data);
    }

    public function getAccount(GetAccountRequest $request): AccountResource
    {
        $request = $request->validated();

        $data = $this->service->get($request);

        return new AccountResource($data);
    }

    public function updateAccount(UpdateAccountRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->update($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function updateImage(UpdateProfileImageRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->updateImage($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function deleteImage(): JsonResponse
    {
        $result = $this->service->deleteImage();
        return response()->json(['success' => $result], $result ? 200 : 400);
    }

}
