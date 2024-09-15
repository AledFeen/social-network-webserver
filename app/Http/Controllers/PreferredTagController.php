<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreferredTag\IgnoreRequest;
use App\Services\PreferredTagService;
use Illuminate\Http\Request;

class PreferredTagController extends Controller
{
    protected PreferredTagService $service;

    public function __construct(PreferredTagService $service)
    {
        $this->service = $service;
    }

    public function ignore(IgnoreRequest $request)
    {
        $request = $request->validated();

        $result = $this->service->ignore($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }
}
