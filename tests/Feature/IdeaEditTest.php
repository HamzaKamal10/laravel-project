<?php

declare(strict_types=1);

use App\Models\Idea;
use App\Models\User;
use Illuminate\Http\UploadedFile;

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

test('a user can successfully update an idea and its relationships via traditional form submission', function (): void {
    $owner = User::factory()->create();
    $idea = Idea::factory()->for($owner)->create([
        'title' => 'Old Title',
        'status' => 'pending',
        'links' => ['https://old.com'],
    ]);

    $idea->steps()->create(['description' => 'Old Step', 'completed' => false]);
    $idea->steps()->create(['description' => 'Remove Me', 'completed' => true]);

    $this->actingAs($owner)
        ->patch(route('ideas.update', $idea), [
            'title' => 'New Title',
            'description' => 'Updated description.',
            'status' => 'in_progress',
            'links' => ['https://new.com'],
            'steps' => [
                [
                    'id' => $idea->steps->first()->id,
                    'description' => 'Updated Step',
                    'completed' => 1,
                ],
                [
                    'id' => null,
                    'description' => 'Brand New Step',
                    'completed' => 0,
                ],
            ],
        ])
        ->assertRedirect(route('ideas.show', $idea));

    $this->assertDatabaseHas('ideas', [
        'id' => $idea->id,
        'title' => 'New Title',
        'description' => 'Updated description.',
        'status' => 'in_progress',
    ]);

    $this->assertDatabaseMissing('steps', ['description' => 'Remove Me']);
    $this->assertDatabaseHas('steps', ['description' => 'Updated Step', 'completed' => 1]);
    $this->assertDatabaseHas('steps', ['description' => 'Brand New Step', 'completed' => 0]);
});

test('a user can update the image of an idea', function (): void {
    Storage::fake('public');

    $owner = User::factory()->create();
    $idea = Idea::factory()->for($owner)->create([
        'title' => 'Test Image',
        'image_path' => 'ideas/old-image.jpg',
    ]);

    Storage::disk('public')->put('ideas/old-image.jpg', 'fake-content');

    $file = UploadedFile::fake()->image('new-image.jpg');

    $this->actingAs($owner)
        ->patch(route('ideas.update', $idea), [
            'title' => 'Test Image',
            'status' => 'pending',
            'image' => $file,
        ]);

    $idea->refresh();

    Storage::disk('public')->assertMissing('ideas/old-image.jpg');
    Storage::disk('public')->assertExists($idea->image_path);
    $this->assertNotEquals('ideas/old-image.jpg', $idea->image_path);
});
