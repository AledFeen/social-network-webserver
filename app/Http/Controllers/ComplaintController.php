<?php

namespace App\Http\Controllers;

use App\Http\Requests\Complaint\BanUserRequest;
use App\Http\Requests\Complaint\CreateComplaintRequest;
use App\Http\Requests\Complaint\GetComplaintRequest;
use App\Http\Requests\Complaint\GetComplaintsRequest;
use App\Http\Requests\Complaint\UpdateComplaintRequest;
use App\Http\Resources\Complaint\ComplaintResource;
use App\Http\Resources\Complaint\PaginatedComplaintResource;
use App\Http\Resources\Messages\PaginatedMessagesResource;
use App\Services\ComplaintService;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    protected ComplaintService $service;
    public function __construct(ComplaintService $service)
    {
        $this->service = $service;
    }

    public function getComplaint(GetComplaintRequest $request): ComplaintResource|\Illuminate\Http\JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->getComplaint($request);

        if (!$result) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return new ComplaintResource($result);
    }

    public function getComplaints(GetComplaintsRequest $request): PaginatedComplaintResource|\Illuminate\Http\JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->getComplaints($request);

        if (!$result) {
            return response()->json(['error' => 'No Rights'], 405);
        }

        return new PaginatedComplaintResource($result);
    }

    public function createComplaint(CreateComplaintRequest $request): \Illuminate\Http\JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->createComplaint($request);

        return response()->json(['success' => $result], $result ? 201 : 400);
    }

    public function updateComplaint(UpdateComplaintRequest $request): \Illuminate\Http\JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->updateComplaint($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }

    public function banUser(BanUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $request = $request->validated();

        $result = $this->service->banUser($request);

        return response()->json(['success' => $result], $result ? 200 : 400);
    }
}
