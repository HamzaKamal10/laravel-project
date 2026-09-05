<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Idea;
use Illuminate\Database\Seeder;

class IdeaSeeder extends Seeder
{
    public function run(): void
    {
        Idea::factory()->count(10)->create();
    }
}
