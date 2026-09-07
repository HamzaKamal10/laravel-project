<?php

use App\Models\Idea;
use App\Models\User;

test('an authenticated user can create an idea with valid data', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'My First Idea',
        'description' => 'A great description.',
        'status' => 'pending',
    ]);

    $response->assertRedirect(route('ideas.index'))
        ->assertSessionHas('success', 'Idea created.');

    $this->assertDatabaseHas('ideas', [
        'title' => 'My First Idea',
        'description' => 'A great description.',
        'status' => 'pending',
        'user_id' => $user->id,
    ]);
});

test('the created idea belongs to the authenticated user', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'Ownership Test',
        'status' => 'in_progress',
    ]);

    $idea = Idea::where('title', 'Ownership Test')->first();

    expect($idea)->not->toBeNull()
        ->and($idea->user_id)->toBe($user->id);
});

test('the user cannot assign ownership through a submitted user_id', function (): void {
    $owner = User::factory()->create();
    $victim = User::factory()->create();

    $this->actingAs($owner)->post(route('ideas.store'), [
        'title' => 'Hijack Attempt',
        'status' => 'pending',
        'user_id' => $victim->id,
    ]);

    $idea = Idea::where('title', 'Hijack Attempt')->first();

    expect($idea)->not->toBeNull()
        ->and($idea->user_id)->toBe($owner->id);
});

test('missing title fails validation', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('ideas.store'), [
        'title' => '',
        'status' => 'pending',
    ]);

    $response->assertSessionHasErrors('title');
});

test('a title longer than 255 characters fails validation', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('ideas.store'), [
        'title' => str_repeat('a', 256),
        'status' => 'pending',
    ]);

    $response->assertSessionHasErrors('title');
});

test('invalid status fails validation', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'Valid Title',
        'status' => 'not_a_real_status',
    ]);

    $response->assertSessionHasErrors('status');
});

test('nullable description is accepted', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'No Description Idea',
        'status' => 'completed',
    ]);

    $response->assertRedirect(route('ideas.index'));

    $this->assertDatabaseHas('ideas', [
        'title' => 'No Description Idea',
        'description' => null,
        'status' => 'completed',
    ]);
});

test('validation errors redirect back with old input', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->from(route('ideas.index'))->post(route('ideas.store'), [
        'title' => '',
        'description' => 'Keep this value.',
        'status' => 'pending',
    ]);

    $response->assertRedirect(route('ideas.index'))
        ->assertSessionHasErrors('title')
        ->assertSessionHasInput('description', 'Keep this value.')
        ->assertSessionHasInput('status', 'pending');
});

test('the ideas index orders ideas newest first', function (): void {
    $user = User::factory()->create();

    $older = Idea::factory()->for($user)->create(['title' => 'Older Idea']);

    // نؤخر الإنشاء قليلاً حتى يختلف created_at.
    $this->travel(1)->minutes();
    $newer = Idea::factory()->for($user)->create(['title' => 'Newer Idea']);

    $response = $this->actingAs($user)->get(route('ideas.index'));

    $response->assertOk()
        ->assertSeeInOrder(['Newer Idea', 'Older Idea']);
});

test('an unauthenticated user cannot store an idea', function (): void {
    $response = $this->post(route('ideas.store'), [
        'title' => 'Sneaky Idea',
        'status' => 'pending',
    ]);

    $response->assertRedirect(route('login'));
    $this->assertDatabaseMissing('ideas', ['title' => 'Sneaky Idea']);
});

test('an idea can be created without links', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'No Links Idea',
        'status' => 'pending',
    ]);

    $idea = Idea::where('title', 'No Links Idea')->first();

    expect($idea)->not->toBeNull()
        ->and($idea->links)->toBeEmpty();
});

test('an idea can be created with one link', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'One Link Idea',
        'status' => 'pending',
        'links' => ['https://example.com'],
    ]);

    $idea = Idea::where('title', 'One Link Idea')->first();

    expect($idea)->not->toBeNull()
        ->and(count($idea->links))->toBe(1)
        ->and($idea->links[0])->toBe('https://example.com');
});

test('an idea can be created with multiple links', function (): void {
    $user = User::factory()->create();

    $links = ['https://example.com', 'https://laravel.com', 'https://github.com'];

    $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'Multi Link Idea',
        'status' => 'completed',
        'links' => $links,
    ]);

    $idea = Idea::where('title', 'Multi Link Idea')->first();

    expect($idea)->not->toBeNull()
        ->and(count($idea->links))->toBe(3)
        ->and($idea->links[0])->toBe('https://example.com')
        ->and($idea->links[1])->toBe('https://laravel.com')
        ->and($idea->links[2])->toBe('https://github.com');
});

test('invalid link URL fails validation', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'Bad Link Idea',
        'status' => 'pending',
        'links' => ['not-a-url'],
    ]);

    $response->assertSessionHasErrors('links.0');
    $this->assertDatabaseMissing('ideas', ['title' => 'Bad Link Idea']);
});

test('links are stored under the authenticated user and not a submitted user_id', function (): void {
    $owner = User::factory()->create();
    $victim = User::factory()->create();

    $this->actingAs($owner)->post(route('ideas.store'), [
        'title' => 'Links Ownership',
        'status' => 'pending',
        'links' => ['https://example.com'],
        'user_id' => $victim->id,
    ]);

    $idea = Idea::where('title', 'Links Ownership')->first();

    expect($idea)->not->toBeNull()
        ->and($idea->user_id)->toBe($owner->id)
        ->and(count($idea->links))->toBe(1);
});
