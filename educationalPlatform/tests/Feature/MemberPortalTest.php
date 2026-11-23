<?php

use App\Models\Member;
use App\Models\User;

it('shows the signed in member their portal', function (): void {
    $member = Member::factory()->create(['email' => 'learner@example.com']);
    $user = User::factory()->create(['email' => $member->email]);

    $this->actingAs($user)
        ->get(route('member.portal'))
        ->assertOk()
        ->assertSee('My account')
        ->assertSee($member->membership_number)
        ->assertSee($member->full_name);
});

it('explains when the account has no linked member profile', function (): void {
    $user = User::factory()->create(['email' => 'unlinked@example.com']);

    $this->actingAs($user)
        ->get(route('member.portal'))
        ->assertOk()
        ->assertSee('Your member profile is not linked yet.');
});
