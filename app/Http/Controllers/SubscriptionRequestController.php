<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionRequest\AcceptSubRequest;
use App\Http\Requests\SubscriptionRequest\GetSubRequest;
use App\Http\Requests\SubscriptionRequest\SubscribeRequest;
use App\Http\Resources\SubscriptionRequest\PaginatedSubRequestDTOResource;
use App\Models\PrivacySettings;
use App\Models\Subscription;
use App\Models\SubscriptionRequest;
use App\Services\SubscriptionRequestService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionRequestController extends Controller
{
    protected SubscriptionRequestService $service;

    public function __construct(SubscriptionRequestService $service)
    {
        $this->service = $service;
    }

    public function subscribe(SubscribeRequest $request): \Illuminate\Http\JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->subscribe($request);

        return response()->json(['success' => $result], $result ? 201 : 400);
    }

    public function acceptRequest(AcceptSubRequest $request): \Illuminate\Http\JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->accept($request);

        return response()->json(['success' => $result], $result ? 201 : 400);
    }

    public function declineRequest(AcceptSubRequest $request): \Illuminate\Http\JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->decline($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function getRequests(GetSubRequest $request): PaginatedSubRequestDTOResource
    {
        $request = $request->validated();
        $data = $this->service->get($request);
        return new PaginatedSubRequestDTOResource($data);
    }

    public function getRequestCount(): \Illuminate\Http\JsonResponse
    {
        $data = $this->service->getCount();
        return response()->json(['requestCount' => $data]);
    }
}
