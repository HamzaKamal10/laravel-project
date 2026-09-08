<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\IdeaStatus;
// نستورد نموذج المستخدم لأن المصنع ينشئ مستخدماً مرتبطاً بكل فكرة تجريبية.
use App\Models\Idea;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Idea>
 */
class IdeaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => IdeaStatus::PENDING->value,
            'links' => [fake()->url()],
        ];
    }
}
