<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\Comment\AddCommentRequest;
use App\Http\Requests\Post\Comment\DeleteCommentRequest;
use App\Http\Requests\Post\Comment\GetCommentRepliesRequest;
use App\Http\Requests\Post\Comment\GetCommentRequest;
use App\Http\Requests\Post\Comment\UpdateCommentRequest;
use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\DeletePostRequest;
use App\Http\Requests\Post\Like\GetLikesRequest;
use App\Http\Requests\Post\Like\LikeRequest;
use App\Http\Requests\Post\UpdateFilesRequest;
use App\Http\Requests\Post\UpdateTagsRequest;
use App\Http\Requests\Post\UpdateTextRequest;
use App\Http\Resources\CommentDTO\PaginatedCommentDTOResource;
use App\Http\Resources\UserDTO\PaginatedUserDTOResource;
use App\Services\CommentService;
use App\Services\LikeService;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    protected PostService $postService;
    protected CommentService $commentService;
    protected LikeService $likeService;

    public function __construct(PostService $postService, CommentService $commentService, LikeService $likeService)
    {
        $this->postService = $postService;
        $this->commentService = $commentService;
        $this->likeService = $likeService;
    }

    public function createPost(CreatePostRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->postService->create($request);

        return response()->json(['success' => $result], $result ? 201 : 400);
    }

    public function updatePostText(UpdateTextRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->postService->updateText($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function updatePostTags(UpdateTagsRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->postService->updateTags($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function updatePostFiles(UpdateFilesRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->postService->updateFiles($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function deletePost(DeletePostRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->postService->delete($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function getComments(GetCommentRequest $request)
    {
        $request = $request->validated();

        $result = $this->commentService->get($request);

        return new PaginatedCommentDTOResource($result);
    }

    public function getCommentReplies(GetCommentRepliesRequest $request)
    {
        $request = $request->validated();

        $result = $this->commentService->getReplies($request);

        return new PaginatedCommentDTOResource($result);
    }

    public function leaveComment(AddCommentRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->commentService->create($request);

        return response()->json(['success' => $result], $result ? 201 : 400);
    }

    public function updateComment(UpdateCommentRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->commentService->update($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function deleteComment(DeleteCommentRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->commentService->delete($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function likePost(LikeRequest $request): JsonResponse
    {
        $request = $request->validated();

        $result = $this->likeService->like($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function getPostLikes(GetLikesRequest $request): PaginatedUserDTOResource
    {
        $request = $request->validated();

        $result = $this->likeService->get($request);

        return new PaginatedUserDTOResource($result);
    }

}
