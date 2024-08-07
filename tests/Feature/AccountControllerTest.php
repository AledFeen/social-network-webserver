<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    public function test_get_my_account(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->actingAs($user)->get('/api/my-account');

        $account = Account::where('user_id', $user->id)->first();
        $expectedData = [
            'data' => [
                'id' => $account->id,
                'user_id' => $user->id,
                'real_name' => $account->real_name,
                'date_of_birth' => $account->date_of_birth,
                'about_me' => $account->about_me,
                'location' => $account->location,
                'image' => $account->image,
                'created_at' => $account->created_at,
                'updated_at' => $account->updated_at
            ],
        ];

        $response->assertStatus(200)
            ->assertJsonFragment($expectedData);
    }


    public function test_get_account(): void
    {
        $user = User::factory()->create();
        $user_one = User::factory()->create();

        Auth::login($user);

        $response = $this->actingAs($user)->get("/api/account?user_id={$user_one->id}");

        $account = Account::where('user_id', $user_one->id)->first();
        $expectedData = [
            'data' => [
                'id' => $account->id,
                'user_id' => $user_one->id,
                'real_name' => $account->real_name,
                'date_of_birth' => $account->date_of_birth,
                'about_me' => $account->about_me,
                'location' => $account->location,
                'image' => $account->image,
                'created_at' => $account->created_at,
                'updated_at' => $account->updated_at
            ],
        ];

        $response->assertStatus(200)
            ->assertJsonFragment($expectedData);
    }

    public function test_update_account(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $location = Location::factory()->create();

        $response = $this->actingAs($user)->put('/api/my-account', [
            'real_name' => 'vsevolod',
            'location' => $location->name,
            'date_of_birth' => null,
            'about_me' => 'hello world',
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('accounts', ['real_name' => 'vsevolod', 'about_me' => 'hello world', 'location' =>  $location->name, 'date_of_birth' => null]);
    }

    public function test_update_image(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->actingAs($user)->post('/api/profile-image', [
            'image' => UploadedFile::fake()->image('test_avatar.jpg')
        ]);

        $account = Account::where('user_id', $user->id)->first();
        Storage::delete('/private/images/accounts/' . $account->image);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_delete_image()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->actingAs($user)->post('/api/profile-image', [
            'image' => UploadedFile::fake()->image('test_avatar.jpg')
        ]);

        $response = $this->actingAs($user)->delete('/api/profile-image');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertDatabaseHas('accounts', ['user_id' => $user->id, 'image' => 'default_avatar']);
    }
}
