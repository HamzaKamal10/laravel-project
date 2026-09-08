<?php

declare(strict_types=1);

use App\Models\Idea;
use App\Models\Step;
use App\Models\User;

test('a guest cannot view an idea', function (): void {
    $idea = Idea::factory()->create();

    $this->get(route('ideas.show', $idea))
        ->assertRedirect(route('login'));
});

test('a guest cannot update an idea', function (): void {
    $idea = Idea::factory()->create();

    $this->patchJson(route('steps.update', Step::factory()->for($idea)->create()))
        ->assertUnauthorized();
});

test('a guest cannot delete an idea', function (): void {
    $idea = Idea::factory()->create();

    $this->delete(route('ideas.destroy', $idea))
        ->assertRedirect(route('login'));
});

test('the idea owner can view the idea', function (): void {
    $owner = User::factory()->create();
    $idea = Idea::factory()->for($owner)->create();

    $this->actingAs($owner)
        ->get(route('ideas.show', $idea))
        ->assertOk();
});

test('the idea owner can update the idea steps', function (): void {
    $owner = User::factory()->create();
    $idea = Idea::factory()->for($owner)->create();
    $step = Step::factory()->for($idea)->create(['completed' => false]);

    $this->actingAs($owner)
        ->patch(route('steps.update', $step))
        ->assertRedirect();

    expect($step->fresh()->completed)->toBeTrue();
});

test('the idea owner can delete the idea', function (): void {
    $owner = User::factory()->create();
    $idea = Idea::factory()->for($owner)->create();

    $this->actingAs($owner)
        ->delete(route('ideas.destroy', $idea))
        ->assertRedirect(route('ideas.index'));

    $this->assertDatabaseMissing('ideas', ['id' => $idea->id]);
});

test('a different authenticated user cannot view the idea', function (): void {
    $owner = User::factory()->create();
    $visitor = User::factory()->create();
    $idea = Idea::factory()->for($owner)->create();

    $this->actingAs($visitor)
        ->get(route('ideas.show', $idea))
        ->assertForbidden();
});

test('a different authenticated user cannot update the idea steps', function (): void {
    $owner = User::factory()->create();
    $visitor = User::factory()->create();
    $idea = Idea::factory()->for($owner)->create();
    $step = Step::factory()->for($idea)->create(['completed' => false]);

    $this->actingAs($visitor)
        ->patch(route('steps.update', $step))
        ->assertForbidden();

    expect($step->fresh()->completed)->toBeFalse();
});

test('a different authenticated user cannot delete the idea', function (): void {
    $owner = User::factory()->create();
    $visitor = User::factory()->create();
    $idea = Idea::factory()->for($owner)->create();

    $this->actingAs($visitor)
        ->delete(route('ideas.destroy', $idea))
        ->assertForbidden();

    $this->assertDatabaseHas('ideas', ['id' => $idea->id]);
});

test('missing ideas return 404', function (): void {
    $owner = User::factory()->create();

    $this->actingAs($owner)
        ->get('/ideas/99999')
        ->assertNotFound();
});
