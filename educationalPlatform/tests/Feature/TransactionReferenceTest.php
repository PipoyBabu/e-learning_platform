<?php

use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Services\TransactionService;

it('does not reuse an existing transaction reference', function (): void {
    $category = MembershipCategory::factory()->create();
    $level = MembershipLevel::factory()->create();
    $member = Member::factory()->create([
        'membership_category_id' => $category->id,
        'membership_level_id' => $level->id,
    ]);

    $first = app(TransactionService::class)->createApplication($member, $level);
    $firstReference = $first->reference_no;

    $second = MembershipTransaction::create([
        'member_id' => $member->id,
        'membership_level_id' => $level->id,
        'type' => $first->type,
        'status' => $first->status,
        'amount_due' => $level->fee,
    ]);

    expect($second->reference_no)->not->toBe($firstReference);
});
