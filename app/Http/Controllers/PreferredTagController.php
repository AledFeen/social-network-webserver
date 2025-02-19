<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreferredTag\AddTagRequest;
use App\Http\Requests\PreferredTag\DeleteTagRequest;
use App\Http\Requests\PreferredTag\IgnoreRequest;
use App\Http\Resources\PreferredTagResource;
use App\Services\PreferredTagService;
use Illuminate\Http\Request;

class PreferredTagController extends Controller
{
    protected PreferredTagService $service;

    public function __construct(PreferredTagService $service)
    {
        $this->service = $service;
    }

    public function get(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $result = $this->service->getTags();

        return PreferredTagResource::collection($result);
    }

    public function add(AddTagRequest $request): \Illuminate\Http\JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->addTag($request);

        return response()->json(['success' => $result], $result ? 201 : 400);
    }

    public function delete(DeleteTagRequest $request): \Illuminate\Http\JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->delete($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function ignore(IgnoreRequest $request): \Illuminate\Http\JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->ignore($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }
}
