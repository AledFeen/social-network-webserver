<?php

namespace App\Services;

use App\Models\Account;
use App\Models\dto\ProfileDTO;
use App\Models\Location;
use App\Services\Location\hasLocation;
use App\Services\Location\MustHaveLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AccountService implements MustHaveLocation
{
    use hasLocation;

    public function getMy()
    {
        return Account::where('user_id', Auth::id())->first();
    }

    public function getProfile($request): ProfileDTO
    {
        $account = Account::where('user_id', $request['user_id'])
            ->with('user.privacy')
            ->first();

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
