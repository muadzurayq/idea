<?php

namespace Database\Factories;

use App\Models\Idea;
use App\Models\step;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<step>
 */
class StepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => Idea::factory(),
            'description' => fake()->paragraph(),
            'completed' => false,
        ];
    }
}
