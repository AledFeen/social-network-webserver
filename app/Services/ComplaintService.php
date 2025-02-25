<?php

namespace App\Services;

use App\Models\BannedUser;
use App\Models\Complaint;
use App\Models\User;
use App\Services\Paginate\PaginatedResponse;
use Illuminate\Support\Facades\Auth;
use function PHPUnit\Framework\isTrue;

class ComplaintService
{
    public function getComplaint(array $request)
    {
        $user = User::where('id', Auth::id())->first();
        if ($user->role == 'admin') {
            return Complaint::where('id', $request['complaint_id'])->first();
        }
        return null;
    }

    public function getComplaints(array $request): ?PaginatedResponse
    {
        $user = User::where('id', Auth::id())->first();
        if ($user->role == 'admin') {
            $complaints = Complaint::query();

            switch ($request['type'] ?? 'all') {
                case 'post':
                    $complaints->whereNotNull('post_id');
                    break;
                case 'comment':
                    $complaints->whereNotNull('comment_id');
                    break;
                case 'message':
                    $complaints->whereNotNull('message_id');
                    break;
                case 'user':
                    $complaints->whereNotNull('user_id')->whereNull('post_id')->whereNull('comment_id')->whereNull('message_id');
                    break;
                case 'all':
                default:
                    break;
            }

            if (!empty($request['date'])) {
                $complaints->whereDate('created_at', $request['date']);
            }

            if (!empty($request['status'])) {
                $complaints->where('status', $request['status']);
            }

            if (!empty($request['measure_status'])) {
                $complaints->where('measure_status', $request['measure_status']);
            }

            $complaints =  $complaints->orderBy('created_at', 'desc')->paginate(5, ['*'], 'page', $request['page_id']);

            return new PaginatedResponse(
                $complaints,
                $complaints->currentPage(),
                $complaints->lastPage(),
                $complaints->total()
            );
        }
        return null;
    }

    public function createComplaint(array $request): bool
    {
        return (bool)Complaint::create([
            'sender_id' => Auth::id(),
            'user_id' => $request['user_id'] ?? null,
            'post_id' => $request['post_id'] ?? null,
            'comment_id' => $request['comment_id'] ?? null,
            'message_id' => $request['message_id'] ?? null,
            'text' => $request['text']
        ]);
    }

    public function updateComplaint(array $request): bool
    {
        $user = User::where('id', Auth::id())->first();
        if ($user->role == 'admin') {
            return (bool)Complaint::where('id', $request['complaint_id'])->update([
                'status' => 'checked',
                'measure_status' => $request['measure_status'],
                'measure' => $request['measure'] ?? null
            ]);
        }
        return false;
    }

    public function banUser(array $request): bool
    {
        $user = User::where('id', Auth::id())->first();
        if ($user && $user->role == 'admin') {
            if(!BannedUser::where('user_id', $request['user_id'])->exists()) {
                return (bool)BannedUser::create([
                    'user_id' => $request['user_id']
                ]);
            } else return false;
        } else return false;
    }

}
