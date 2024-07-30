<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\GetAccountRequest;
use App\Http\Requests\Account\UpdateAccountRequest;
use App\Http\Requests\Account\UpdateProfileImageRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{

    protected $service;

    public function __construct(AccountService $service)
    {
        $this->service = $service;
    }

    public function getMyAccount()
    {
        $data = $this->service->getMy();

        return new AccountResource($data);
    }

    public function getAccount(GetAccountRequest $request)
    {
        $request = $request->validated();

        $data = $this->service->get($request);

        return new AccountResource($data);
    }

    public function updateAccount(UpdateAccountRequest $request) {
        $request = $request->validated();

        $result = $this->service->update($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function updateImage(UpdateProfileImageRequest $request) {
        $request = $request->validated();

        $result = $this->service->updateImage($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function deleteImage() {
        $result = $this->service->deleteImage();
        return response()->json(['success' => $result], $result ? 200 : 400);
    }

}
