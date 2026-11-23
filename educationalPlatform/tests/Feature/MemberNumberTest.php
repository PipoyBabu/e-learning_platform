<?php

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;

it('does not reuse a membership number after a member is deleted', function (): void {
    $category = MembershipCategory::factory()->create();
    $level = MembershipLevel::factory()->create();
    $first = Member::factory()->create([
        'membership_category_id' => $category->id,
        'membership_level_id' => $level->id,
    ]);

    $firstNumber = $first->membership_number;
    $first->delete();

    $second = Member::factory()->create([
        'membership_category_id' => $category->id,
        'membership_level_id' => $level->id,
    ]);

    expect($second->membership_number)->not->toBe($firstNumber);
});
