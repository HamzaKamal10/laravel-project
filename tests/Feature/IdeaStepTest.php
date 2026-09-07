<?php

use App\Models\Idea;
use App\Models\Step;
use App\Models\User;

test('an idea can be created without steps', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'Idea No Steps',
        'status' => 'pending',
    ]);

    $idea = Idea::where('title', 'Idea No Steps')->first();

    expect($idea)->not->toBeNull()
        ->and($idea->steps)->toBeEmpty();
});

test('an idea can be created with multiple steps', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'Idea With Steps',
        'status' => 'pending',
        'steps' => ['Step 1', 'Step 2'],
    ]);

    $idea = Idea::where('title', 'Idea With Steps')->first();

    expect($idea)->not->toBeNull()
        ->and($idea->steps)->toHaveCount(2)
        ->and($idea->steps[0]->description)->toBe('Step 1')
        ->and($idea->steps[0]->completed)->toBeFalse()
        ->and($idea->steps[1]->description)->toBe('Step 2')
        ->and($idea->steps[1]->completed)->toBeFalse();
});

test('empty step values fail validation', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'Idea Empty Steps',
        'status' => 'pending',
        'steps' => [''],
    ]);

    $response->assertSessionHasErrors('steps.0');
    $this->assertDatabaseMissing('ideas', ['title' => 'Idea Empty Steps']);
});

test('a step description longer than 255 characters fails validation', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'Idea Long Step',
        'status' => 'pending',
        'steps' => [str_repeat('a', 256)],
    ]);

    $response->assertSessionHasErrors('steps.0');
});

test('an authorized user can toggle a step', function (): void {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create();
    $step = Step::factory()->for($idea)->create(['completed' => false]);

    $response = $this->actingAs($user)->patch(route('steps.update', $step));

    $response->assertRedirect();
    $step->refresh();

    expect($step->completed)->toBeTrue();

    // Toggle back
    $this->actingAs($user)->patch(route('steps.update', $step));
    $step->refresh();
    expect($step->completed)->toBeFalse();
});

test('an unauthorized user cannot toggle a step', function (): void {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $idea = Idea::factory()->for($owner)->create();
    $step = Step::factory()->for($idea)->create(['completed' => false]);

    $response = $this->actingAs($intruder)->patch(route('steps.update', $step));

    $response->assertForbidden();
    $step->refresh();
    expect($step->completed)->toBeFalse();
});

test('an unauthenticated user cannot toggle a step', function (): void {
    $owner = User::factory()->create();
    $idea = Idea::factory()->for($owner)->create();
    $step = Step::factory()->for($idea)->create(['completed' => false]);

    $response = $this->patch(route('steps.update', $step));

    $response->assertRedirect(route('login'));
});

test('updating a non-existent step returns 404', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patch(route('steps.update', 9999));

    $response->assertNotFound();
});
