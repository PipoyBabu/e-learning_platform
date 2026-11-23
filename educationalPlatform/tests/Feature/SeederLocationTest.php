<?php

use App\Models\Member;
use Database\Seeders\DatabaseSeeder;

it('seeds all demo member locations within NCR', function (): void {
    $this->seed(DatabaseSeeder::class);

    $members = Member::query()->get(['address', 'latitude', 'longitude']);

    expect($members)->toHaveCount(12)
        ->and($members->every(fn (Member $member): bool => $member->address !== null && $member->latitude !== null && $member->longitude !== null))->toBeTrue()
        ->and($members->every(fn (Member $member): bool => $member->latitude >= 14.35 && $member->latitude <= 14.80 && $member->longitude >= 120.90 && $member->longitude <= 121.20))->toBeTrue();
});
