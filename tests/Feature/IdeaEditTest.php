<?php

declare(strict_types=1);

use App\Models\Idea;
use App\Models\User;

test('the edit idea modal is visible and populated for the owner', function (): void {
    $owner = User::factory()->create();
    $idea = Idea::factory()->for($owner)->create([
        'title' => 'My Awesome Idea',
        'description' => 'A great description here.',
    ]);

    $this->actingAs($owner)
        ->get(route('ideas.show', $idea))
        ->assertOk()
        ->assertSee('Edit Idea')
        ->assertSee('My Awesome Idea')
        ->assertSee('A great description here.')
        ->assertSee(route('ideas.update', $idea));
});

test('a user can successfully update an idea via traditional form submission', function (): void {
    $owner = User::factory()->create();
    $idea = Idea::factory()->for($owner)->create([
        'title' => 'Old Title',
        'status' => 'pending',
    ]);

    $this->actingAs($owner)
        ->patch(route('ideas.update', $idea), [
            'title' => 'New Title',
            'description' => 'Updated description.',
            'status' => 'in_progress',
        ])
        ->assertRedirect(route('ideas.show', $idea));

    $this->assertDatabaseHas('ideas', [
        'id' => $idea->id,
        'title' => 'New Title',
        'description' => 'Updated description.',
        'status' => 'in_progress',
    ]);
});
