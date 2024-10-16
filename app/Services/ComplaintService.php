<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\User;
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

    public function getComplaints(array $request): \Illuminate\Database\Eloquent\Collection|bool|array
    {
        $user = User::where('id', Auth::id())->first();
        if ($user->role == 'admin') {
            $complaints = Complaint::query();

            switch ($request['type'] ?? 'all') {
                case 'user':
                    $complaints->whereNotNull('user_id');
                    break;
                case 'post':
                    $complaints->whereNotNull('post_id');
                    break;
                case 'comment':
                    $complaints->whereNotNull('comment_id');
                    break;
                case 'message':
                    $complaints->whereNotNull('message_id');
                    break;
                case 'all':
                default:
                    break;
            }

            if(!empty($request['date'])) {
                $complaints->whereDate('created_at', $request['date']);
            }

            if(!empty($request['status'])) {
                $complaints->where('status', $request['status']);
            }

            if(!empty($request['measure_status'])) {
                $complaints->where('measure_status', $request['measure_status']);
            }

            return $complaints->orderBy('created_at', 'desc')->get();
        }
        return false;
    }

    public function createComplaint(array $request): bool
    {
        $user = User::where('id', Auth::id())->first();
        if ($user->role == 'admin') {
            return (bool)Complaint::create([
                'sender_id' => Auth::id(),
                'user_id' => $request['user_id'] ?? null,
                'post_id' => $request['post_id'] ?? null,
                'comment_id' => $request['comment_id'] ?? null,
                'message_id' => $request['message_id'] ?? null,
                'text' => $request['text']
            ]);
        }
        return false;

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


}
