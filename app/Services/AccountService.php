<?php

namespace App\Services;

use App\Models\Account;
use App\Models\dto\ProfileDTO;
use App\Models\dto\UserDTO;
use App\Models\Location;
use App\Models\User;
use App\Services\Blacklist\checkingBlacklist;
use App\Services\Location\hasLocation;
use App\Services\Location\MustHaveLocation;
use App\Services\Paginate\PaginatedResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AccountService implements MustHaveLocation
{
    use hasLocation;
    use checkingBlacklist;

    public function findProfile($request): PaginatedResponse
    {
        $blockedByIds = $this->blockedBy();
        $name = $request['search_request'];

        $paginatedUsers = User::where(function($query) use ($name) {
            $query->WhereHas('account', function($query) use ($name) {
                $query->where('real_name', 'like', '%' . $name . '%');
            })
            ->orWhere('name', 'like', '%' . $name . '%');
        })
            ->whereNotIn('id', $blockedByIds)
            ->with('account')
            ->paginate(15, ['*'], 'page', $request['page_id']);

        $users = $paginatedUsers->map(function ($user) {
            return new UserDTO(
                $user->id,
                $user->name,
                $user->account->image
            );
        });

        return new PaginatedResponse(
            $users,
            $paginatedUsers->currentPage(),
            $paginatedUsers->lastPage(),
            $paginatedUsers->total()
        );
    }

    public function getProfile($request): ?ProfileDTO
    {
        $blockedByIds = $this->blockedBy();

        $account = Account::where('user_id', $request['user_id'])
            ->whereNotIn('user_id', $blockedByIds)
            ->with('user.privacy')
            ->first();

        if(!$account) {
            return null;
        }

        return new ProfileDTO(
            $account->user_id,
            $account->user->name,
            $account->image,
            $account->birthday,
            $account->about,
            $account->real_name,
            $account->location,
            $account->user->privacy->account_type,
            $account->user->privacy->who_can_message
        );
    }

    public function getMy()
    {
        return Account::where('user_id', Auth::id())->first();
    }

    public function update($request): bool
    {
        $location = $request['location'] ? $this->checkLocation($request['location']) : null;

        $updated = Account::where('user_id', Auth::id())->update([
            'real_name' => $request['real_name'],
            'location' => $location,
            'date_of_birth' => $request['date_of_birth'],
            'about_me' => $request['about_me'],
        ]);

        return (bool)$updated;
    }

    public function updateImage($request): bool
    {
        $oldData = $this->getMy();
        if ($oldData->image != 'default_avatar') {
            $success = $this->deleteOldImage($oldData->image);
            if ($success) {
                $imageName = $this->saveImage($request['image']);
                return $this->setImage($imageName);
            }
        } else {
            $imageName = $this->saveImage($request['image']);
            return $this->setImage($imageName);
        }
        return false;
    }

    public function deleteImage(): bool
    {
        $oldData = $this->getMy();
        if ($oldData->image != 'default_avatar') {
            $success = $this->deleteOldImage($oldData->image);
            $imageName = 'default_avatar';
            if ($success) {
                $updated = Account::where('user_id', Auth::id())->update([
                    'image' => $imageName
                ]);
                return (bool)$updated;
            }
            return false;
        }
        return false;
    }

    protected function setImage($imageName): bool
    {
        if ($imageName) {
            $updated = Account::where('user_id', Auth::id())->update([
                'image' => $imageName
            ]);
            return (bool)$updated;
        }
        return false;
    }

    protected function deleteOldImage($old): bool
    {
        return Storage::delete('/private/images/accounts/' . $old);
    }

    protected function saveImage($new): string|bool
    {
        $imagePath = Storage::put('/private/images/accounts', $new);
        return $imagePath ? basename($imagePath) : false;
    }
}
