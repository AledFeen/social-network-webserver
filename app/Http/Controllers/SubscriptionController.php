<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscription\SubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Models\dto\UserDTO;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected $service;

    public function __construct(SubscriptionService $service)
    {
        $this->service = $service;
    }

    public function subscribeUser(SubscriptionRequest $request)
    {
        $request = $request->validated();

        $result = $this->service->subscribe($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function unsubscribeUser(SubscriptionRequest $request)
    {
        $request = $request->validated();

        $result = $this->service->unsubscribe($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function deleteSubscriber(SubscriptionRequest $request)
    {
        $request = $request->validated();

        $result = $this->service->deleteSubscriber($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function getSubscribers(SubscriptionRequest $request)
    {
        $request = $request->validated();

        $followers = $this->service->subscribers($request);

        return SubscriptionResource::collection($followers);
    }

    public function getSubscriptions(SubscriptionRequest $request)
    {
        $request = $request->validated();

        $following = $this->service->subscriptions($request);

        return SubscriptionResource::collection($following);
    }
}
