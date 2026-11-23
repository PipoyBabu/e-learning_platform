<?php

use App\Models\User;

it('allows a user to register with an accessible email address', function (): void {
    $this->post(route('register.store'), [
        'name' => 'New Learner',
        'email' => 'learner@gmail.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertRedirect(route('verification.notice'));

    $this->assertAuthenticated();
    expect(User::where('email', 'learner@gmail.com')->value('email_verified_at'))->toBeNull();
});
