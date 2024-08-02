<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AccountService
{
    public function getMy()
    {
        return Account::where('user_id', Auth::id())->first();
    }

    public function get($request)
    {
        return Account::where('user_id', $request['user_id'])->first();
    }

    public function update($request): bool
    {
        $location = $this->checkLocation($request['location']);
        if ($location) {
            $updated = Account::where('user_id', Auth::id())->update([
                'real_name' => $request['real_name'],
                'location' => $request['location'],
                'date_of_birth' => $request['date_of_birth'],
                'about_me' => $request['about_me'],
            ]);

            return (bool)$updated;
        }
        return false;
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

    protected function checkLocation(string $location): bool
    {
        if (!Location::where('name', $location)->first()) {
            Location::create(['name' => $location]);
            return true;
        }
        return true;
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
