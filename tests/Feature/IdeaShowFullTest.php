<?php

use App\Models\Idea;
use App\Models\Step;
use App\Models\User;

test('it renders full idea', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create([
        'links' => ['http://example.com'],
    ]);
    Step::factory()->for($idea)->create(['completed' => false]);

    $response = $this->actingAs($user)->withoutExceptionHandling()->get(route('ideas.show', $idea));
    $response->assertOk();
});
