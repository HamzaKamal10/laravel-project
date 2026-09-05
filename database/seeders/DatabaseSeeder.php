<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // إنشاء مستخدم تجريبي إن لم يكن موجودًا بالفعل
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // توليد 5 أفكار عشوائية مع التأكد من وجود الحقول المطلوبة
        Idea::factory()
            ->count(5)
            ->create([
                'title' => fake()->sentence(),
                'description' => fake()->paragraph(),
                'status' => fake()->randomElement(['Pending', 'In Progress', 'Completed']),
            ]);
    }
}
