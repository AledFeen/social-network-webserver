<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\SubscriptionGetRequest;
use App\Http\Requests\Subscription\SubscriptionRequest;
use App\Http\Resources\UserDTO\PaginatedUserDTOResource;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    protected SubscriptionService $service;

    public function __construct(SubscriptionService $service)
    {
        $this->service = $service;
    }

    public function subscribeUser(SubscriptionRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->subscribe($request);

        return response()->json(['success' => $result], $result ? 201 : 400);
    }

    public function unsubscribeUser(SubscriptionRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->unsubscribe($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function deleteSubscriber(SubscriptionRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->deleteSubscriber($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function getSubscribers(SubscriptionGetRequest $request): PaginatedUserDTOResource
    {
        $request = $request->validated();

        $followers = $this->service->subscribers($request);

        return new PaginatedUserDTOResource($followers);
    }

    public function getSubscriptions(SubscriptionGetRequest $request): PaginatedUserDTOResource
    {
        $request = $request->validated();

        $followings = $this->service->subscriptions($request);

        return new PaginatedUserDTOResource($followings);
    }

    public function checkRelations(SubscriptionRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->checkRelations($request);

        return response()->json($result);
    }
}
