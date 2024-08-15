<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\DeletePostRequest;
use App\Http\Requests\Post\UpdateFilesRequest;
use App\Http\Requests\Post\UpdateTextRequest;
use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    protected $service;

    public function __construct(PostService $service)
    {
        $this->service = $service;
    }

    public function createPost(CreatePostRequest $request)
    {
        $request = $request->validated();

        $result = $this->service->create($request);

        return response()->json(['success' => $result], $result ? 201 : 400);
    }

    public function updatePostText(UpdateTextRequest $request)
    {
        $request = $request->validated();

        $result = $this->service->updateText($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function updatePostFiles(UpdateFilesRequest $request)
    {
        $request = $request->validated();

        $result = $this->service->updateFiles($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function deletePost(DeletePostRequest $request)
    {
        $request = $request->validated();

        $result = $this->service->delete($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

}
