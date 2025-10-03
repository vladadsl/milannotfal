<?php

namespace Tests\Feature;

use App\Models\EmergencyProfile;
use App\Models\MedicalCondition;
use App\Models\Medication;
use App\Models\User;
use App\Services\EmergencyResourceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class EmergencyProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_emergency_profile_page(): void
    {
        $user = User::factory()->create();

        $service = app(EmergencyResourceService::class);
        $service->bootstrap($user, '2468');

        $response = $this->actingAs($user)->get('/emergency/profile');

        $card = $user->refresh()->emergencyCard;

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Emergency/Profile')
            ->where('card.status', 'active')
            ->where('card.card_uuid', $card->card_uuid)
            ->where('publicUrl', route('card.public.show', $card->qr_token))
        );
    }

    public function test_user_can_regenerate_pin(): void
    {
        $user = User::factory()->create();

        $service = app(EmergencyResourceService::class);
        $service->bootstrap($user, '1357');

        $originalHash = $user->pin_hash;

        $response = $this->actingAs($user)->post('/emergency/profile/pin');

        $response->assertRedirect();
        $response->assertSessionHas('flash.pin');

        $user->refresh();

        $this->assertNotSame($originalHash, $user->pin_hash);
        $this->assertMatchesRegularExpression('/^\d{4}$/', session('flash.pin'));
    }

    public function test_public_card_unlock_flow(): void
    {
        $user = User::factory()->create();

        /** @var EmergencyResourceService $service */
        $service = app(EmergencyResourceService::class);
        $service->bootstrap($user, '4321');

        $profile = $user->refresh()->emergencyProfile;

        MedicalCondition::factory()->create([
            'emergency_profile_id' => $profile->id,
            'name' => 'Type 1 Diabetes',
            'is_critical' => true,
        ]);

        Medication::factory()->create([
            'emergency_profile_id' => $profile->id,
            'name' => 'Insulin',
            'dosage' => '10 units',
        ]);

        $token = $user->emergencyCard->qr_token;

        $this->get(route('card.public.show', $token))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/Card')
                ->where('pinVerified', false)
                ->where('profile.pin_protected', null)
            );

        $this->post(route('card.public.unlock', $token), ['pin' => '4321'])
            ->assertRedirect(route('card.public.show', $token));

        $this->get(route('card.public.show', $token))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('pinVerified', true)
                ->has('profile.pin_protected.conditions')
                ->has('profile.pin_protected.medications')
            );
    }
}
