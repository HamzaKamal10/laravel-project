<?php

declare(strict_types=1);

use App\Models\Idea;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('an idea can be created without an image', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'No Image Idea',
        'status' => 'pending',
    ]);

    $response->assertRedirect(route('ideas.index'));

    $idea = Idea::where('title', 'No Image Idea')->first();

    expect($idea)->not->toBeNull()
        ->and($idea->image_path)->toBeNull();
});

test('an idea can be created with a valid image', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    $image = UploadedFile::fake()->image('featured.jpg', 800, 600);

    $response = $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'Image Idea',
        'status' => 'pending',
        'image' => $image,
    ]);

    $response->assertRedirect(route('ideas.index'));

    $idea = Idea::where('title', 'Image Idea')->first();

    expect($idea)->not->toBeNull()
        ->and($idea->image_path)->not->toBeNull()
        ->and($idea->image_path)->toStartWith('ideas/');

    Storage::disk('public')->assertExists($idea->image_path);
});

test('the image is displayed on the idea detail page', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    $image = UploadedFile::fake()->image('show-test.jpg', 400, 300);

    $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'Show Page Image Idea',
        'status' => 'pending',
        'image' => $image,
    ]);

    $idea = Idea::where('title', 'Show Page Image Idea')->first();

    $response = $this->actingAs($user)->get(route('ideas.show', $idea));

    $response->assertOk()
        ->assertSee($idea->image_path);
});

test('the image is displayed on idea cards', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    $image = UploadedFile::fake()->image('card-test.jpg', 400, 300);

    $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'Card Image Idea',
        'status' => 'pending',
        'image' => $image,
    ]);

    $response = $this->actingAs($user)->get(route('ideas.index'));

    $idea = Idea::where('title', 'Card Image Idea')->first();

    $response->assertOk()
        ->assertSee($idea->image_path);
});

test('a non-image file is rejected', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

    $response = $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'Bad File Idea',
        'status' => 'pending',
        'image' => $file,
    ]);

    $response->assertSessionHasErrors('image');
    $this->assertDatabaseMissing('ideas', ['title' => 'Bad File Idea']);
});

test('an image larger than 5 megabytes is rejected', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    // 6 MB image exceeds the 5 MB limit.
    $image = UploadedFile::fake()->image('huge.jpg')->size(6 * 1024);

    $response = $this->actingAs($user)->post(route('ideas.store'), [
        'title' => 'Huge Image Idea',
        'status' => 'pending',
        'image' => $image,
    ]);

    $response->assertSessionHasErrors('image');
    $this->assertDatabaseMissing('ideas', ['title' => 'Huge Image Idea']);
});

test('ideas without images still render correctly on the index page', function (): void {
    $user = User::factory()->create();
    Idea::factory()->for($user)->create(['title' => 'Plain Idea', 'image_path' => null]);

    $response = $this->actingAs($user)->get(route('ideas.index'));

    $response->assertOk()
        ->assertSee('Plain Idea');
});

test('ideas without images still render correctly on the show page', function (): void {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create(['image_path' => null]);

    $response = $this->actingAs($user)->get(route('ideas.show', $idea));

    $response->assertOk()
        ->assertSee($idea->title);
});
