<?php

use App\Models\User;

it('shows the login page to guests', function (): void {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Sign in to your account')
        ->assertSee('Sign in');
});

it('redirects guests away from protected pages', function (): void {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

it('allows a user to log in with valid credentials', function (): void {
    $user = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $this->post(route('login.store'), [
        'email' => 'admin@example.com',
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('rejects invalid credentials', function (): void {
    User::factory()->create([
        'email' => 'admin@example.com',
        'password' => 'password',
    ]);

    $this->from(route('login'))
        ->post(route('login.store'), [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('logs an authenticated user out', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
