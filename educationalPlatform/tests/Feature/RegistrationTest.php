<?php

use App\Models\Member;
use App\Models\User;

it('allows an active member to register and sends verification email', function (): void {
    $member = Member::factory()->create(['email' => 'learner@example.com']);

    $this->post(route('register.store'), [
        'name' => $member->full_name,
        'email' => $member->email,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('verification.notice'));

    $this->assertAuthenticated();
    expect(User::where('email', $member->email)->exists())->toBeTrue();
});

it('rejects registration for an email without an active member profile', function (): void {
    $this->from(route('register'))
        ->post(route('register.store'), [
            'name' => 'Unlinked User',
            'email' => 'unlinked@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertRedirect(route('register'))
        ->assertSessionHasErrors('email');
});
