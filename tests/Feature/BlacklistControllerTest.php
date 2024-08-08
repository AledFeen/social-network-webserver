<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\BlockedUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlacklistControllerTest extends TestCase
{
    use RefreshDatabase;
    public function test_get_blocked_users(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();
        $user_second = User::factory()->create();

        BlockedUser::factory()->create(
            [
                'user_id' => $user->id,
                'blocked_id' => $user_first->id
            ]
        );

        BlockedUser::factory()->create(
            [
                'user_id' => $user->id,
                'blocked_id' => $user_second->id
            ]
        );

        $account_first = Account::where('user_id', $user_first->id)->first();
        $account_second = Account::where('user_id', $user_second->id)->first();

        $expectedData = [
            ['id' => $user_first->id, 'name' => $user_first->name, 'image' => $account_first->image],
            ['id' => $user_second->id, 'name' => $user_second->name,'image' => $account_second->image]
        ];

        $response = $this->actingAs($user)->get('/api/blocked-users');
        $response->assertStatus(200)
            ->assertJsonFragment($expectedData[0])
            ->assertJsonFragment($expectedData[1]);
    }

    public function test_block_user(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();

        $response = $this->actingAs($user)->post('/api/block-user', ['user_id' => $user_first->id]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('blocked_users', [
            'user_id' => $user->id,
            'blocked_id' => $user_first->id
        ]);
    }

    public function test_unblock_user(): void
    {
        $user = User::factory()->create();
        $user_first = User::factory()->create();

        BlockedUser::factory()->create(
            [
                'user_id' => $user->id,
                'blocked_id' => $user_first->id
            ]
        );

        $response = $this->actingAs($user)->delete( "/api/unblock-user?user_id={$user_first->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('blocked_users', ['blocked_id'=> $user_first->id]);
    }
}
