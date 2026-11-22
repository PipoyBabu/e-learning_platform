<?php

namespace Database\Factories;

use App\Models\MembershipLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MembershipLevel> */
class MembershipLevelFactory extends Factory
{
    protected $model = MembershipLevel::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'fee' => fake()->randomFloat(2, 500, 5000),
            'validity_months' => fake()->randomElement([3, 6, 12]),
            'benefits' => fake()->sentence(),
        ];
    }
}
