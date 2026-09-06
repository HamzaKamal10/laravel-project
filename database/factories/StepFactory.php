<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Idea;
use App\Models\Step;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Step>
 */
class StepFactory extends Factory
{
    /**
     * توليد بيانات وهمية لاختبار الخطوات دون إدخال بيانات حقيقية.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // ينشئ المصنع فكرة مرتبطة تلقائياً بالخطوة الوهمية.
            'idea_id' => Idea::factory(),
            // يولد Faker وصفاً تجريبياً للخطوة.
            'description' => fake()->sentence(),
            // تبدأ الخطوة غير مكتملة حتى يغيرها الاختبار عند الحاجة.
            'completed' => false,
        ];
    }
}
