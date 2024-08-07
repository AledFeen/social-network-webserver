<?php

namespace Tests\Feature;

use App\Models\PrivacySettings;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PrivacySettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_settings()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->actingAs($user)->get('/api/privacy-settings', ['user_id' => $user->id]);

        $privacySettings = PrivacySettings::where('user_id', $user->id)->first();
        $expectedData = [
            'data' => [
                'id' => $privacySettings->id,
                'user_id' => $privacySettings->user_id,
                'account_type' => $privacySettings->account_type,
                'who_can_comment' => $privacySettings->who_can_comment,
                'who_can_repost' => $privacySettings->who_can_repost,
                'who_can_message' => $privacySettings->who_can_message,
            ],
        ];

        $response->assertStatus(200)
            ->assertJson($expectedData);
    }

    public function test_update_settings()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->actingAs($user)->put('/api/privacy-settings', [
            'account_type' => 'private',
            'who_can_comment' => 'only_subscribers',
            'who_can_repost' => 'only_subscribers',
            'who_can_message' => 'only_subscribers'
        ]);

        $response->assertStatus(200)
            ->assertJson([
            'success' => true
        ]);;
    }

    public function test_update_privacy_setting_request()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->actingAs($user, 'web')
            ->put('/api/privacy-settings', [
                'account_type' => 'private',
            ]);

        $response->assertStatus(302);
    }

    public function test_update_privacy_setting_uncorrected_value()
    {
        $user = User::factory()->create();
        Auth::login($user);

        $this->assertTrue(Auth::check());

        $response = $this->actingAs($user)->put('/api/privacy-settings', [
            'account_type' => 'private',
            'who_can_comment' => 'only_subscribers',
            'who_can_repost' => 'only_subscribers',
            'who_can_message' => 'i', //uncorrected
        ]);

        $response->assertStatus(500);
    }
}
