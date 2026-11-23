<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IdeaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // أو ربطه بمستخدم موجود مسبقاً
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => fake()->randomElement(['pending', 'in_progress', 'completed']),
            'links' => [fake()->url(), fake()->url()],
        ];
    }
}
