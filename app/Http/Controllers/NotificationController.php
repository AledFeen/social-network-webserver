<?php

namespace App\Http\Controllers;

use App\Http\Resources\Notification\CommentNotificationDTOResource;
use App\Http\Resources\Notification\FollowNotificationDTOResource;
use App\Http\Resources\Notification\LikeNotificationDTOResource;
use App\Http\Resources\Notification\RepostNotificationDTOResource;
use App\Models\dto\CommentNotificationDTO;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function getFollowers(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $data = $this->service->getFollowers();
        return FollowNotificationDTOResource::collection($data);
    }

    public function getLikes(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $data = $this->service->getLikes();
        return LikeNotificationDTOResource::collection($data);
    }

    public function getComments(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $data = $this->service->getComments();
        return CommentNotificationDTOResource::collection($data);
    }

    public function getCommentReplies(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $data = $this->service->getCommentReplies();
        return CommentNotificationDTOResource::collection($data);
    }

    public function getReposts(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $data = $this->service->getReposts();
        return RepostNotificationDTOResource::collection($data);
    }

    public function deleteFollowers(): \Illuminate\Http\JsonResponse
    {
        $result = $this->service->deleteFollowers();
        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function deleteLikes(): \Illuminate\Http\JsonResponse
    {
        $result = $this->service->deleteLikes();
        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function deleteComment(): \Illuminate\Http\JsonResponse
    {
        $result = $this->service->deleteComment();
        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function deleteReplies(): \Illuminate\Http\JsonResponse
    {
        $result = $this->service->deleteReplies();
        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function deleteReposts(): \Illuminate\Http\JsonResponse
    {
        $result = $this->service->deleteReposts();
        return response()->json(['success' => $result], $result ? 200 : 400);
    }

}
