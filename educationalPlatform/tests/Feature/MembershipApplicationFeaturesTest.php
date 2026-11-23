<?php

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\User;
use App\Notifications\MembershipApplicationSubmitted;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

it('stores a membership application and sends its notification', function (): void {
    Notification::fake();
    $user = User::factory()->create();
    $category = MembershipCategory::factory()->create();
    $level = MembershipLevel::factory()->create();
    $member = Member::factory()->create([
        'membership_category_id' => $category->id,
        'membership_level_id' => $level->id,
    ]);

    $this->actingAs($user)
        ->post(route('transactions.store.application'), [
            'member_id' => $member->id,
            'membership_level_id' => $level->id,
        ])
        ->assertRedirect();

    Notification::assertSentOnDemand(MembershipApplicationSubmitted::class);
});

it('returns weather data through the protected server endpoint', function (): void {
    config(['services.openweather.key' => 'test-weather-key']);
    Http::fake([
        'api.openweathermap.org/*' => Http::response([
            'name' => 'Manila',
            'main' => ['temp' => 29, 'humidity' => 70],
            'weather' => [['description' => 'light rain']],
        ]),
    ]);

    $response = $this->actingAs(User::factory()->create())
        ->getJson(route('weather.show', ['lat' => 14.5995, 'lon' => 120.9842]));

    $response->assertOk()->assertJson([
        'place' => 'Manila',
        'temperature' => 29,
        'description' => 'light rain',
        'humidity' => 70,
    ]);
});

it('validates weather coordinates', function (): void {
    $this->actingAs(User::factory()->create())
        ->getJson(route('weather.show', ['lat' => 120, 'lon' => 200]))
        ->assertUnprocessable();
});
