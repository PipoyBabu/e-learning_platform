<?php

namespace Database\Factories;

use App\Models\MembershipCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MembershipCategory> */
class MembershipCategoryFactory extends Factory
{
    protected $model = MembershipCategory::class;

    public function definition(): array
    {
        return ['name' => fake()->unique()->word(), 'description' => fake()->sentence()];
    }
}
