<?php

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

it('shows the signed in member their portal', function (): void {
    $member = Member::factory()->create(['email' => 'learner@example.com']);
    $user = User::factory()->create(['role' => 'member', 'email' => $member->email]);

    $this->actingAs($user)
        ->get(route('member.portal'))
        ->assertOk()
        ->assertSee('My account')
        ->assertSee($member->membership_number)
        ->assertSee($member->full_name)
        ->assertSee('No membership activity yet.');
});

it('explains when the account has no linked member profile', function (): void {
    $user = User::factory()->create(['role' => 'member', 'email' => 'unlinked@example.com']);

    $this->actingAs($user)
        ->get(route('member.portal'))
        ->assertOk()
        ->assertSee('Choose a membership offer')
        ->assertSee('Apply now');
});

it('lets a verified user create a membership application from an offer', function (): void {
    Notification::fake();
    $category = MembershipCategory::factory()->create();
    $level = MembershipLevel::factory()->create(['fee' => 1500]);
    $user = User::factory()->create(['role' => 'member', 'email' => 'applicant@example.com']);

    $this->actingAs($user)
        ->post(route('member.application.store'), [
            'first_name' => 'New',
            'last_name' => 'Applicant',
            'phone' => '09123456789',
            'membership_category_id' => $category->id,
            'membership_level_id' => $level->id,
        ])
        ->assertRedirect();

    $member = Member::where('email', $user->email)->first();
    expect($member)->not->toBeNull()
        ->and($member->transactions()->where('amount_due', 1500)->exists())->toBeTrue();
});

it('lets a member update their account and synchronizes their member email', function (): void {
    $member = Member::factory()->create(['email' => 'old@example.com']);
    $user = User::factory()->create(['role' => 'member', 'email' => $member->email, 'password' => 'password']);

    $this->actingAs($user)
        ->patch(route('member.account.update'), [
            'name' => 'Updated Member',
            'email' => 'new@example.com',
        ])
        ->assertRedirect(route('verification.notice'));

    expect($user->fresh()->email)->toBe('new@example.com')
        ->and($member->fresh()->email)->toBe('new@example.com')
        ->and($user->fresh()->email_verified_at)->toBeNull();
});

it('lets a member delete only their login account', function (): void {
    $member = Member::factory()->create(['email' => 'delete-me@example.com']);
    $user = User::factory()->create(['role' => 'member', 'email' => $member->email, 'password' => 'password']);

    $this->actingAs($user)
        ->delete(route('member.account.destroy'), ['current_password' => 'password'])
        ->assertRedirect(route('login'));

    expect(User::find($user->id))->toBeNull()
        ->and(Member::find($member->id))->not->toBeNull();
});

it('blocks administrators from member account settings', function (): void {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('member.account.edit'))
        ->assertForbidden();
});
