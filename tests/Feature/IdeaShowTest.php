<?php

use App\Models\Idea;
use App\Models\User;

test('an authenticated owner can view an idea with its details and links', function (): void {
    // ننشئ فكرة ببيانات متعددة للتأكد من عرض التفاصيل والروابط كلها.
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create([
        'links' => ['https://example.com/one', 'https://example.com/two'],
    ]);

    $response = $this->actingAs($user)->get(route('ideas.show', $idea));

    $response->assertOk()
        ->assertSee($idea->title)
        ->assertSee($idea->description)
        ->assertSee($idea->status->label())
        ->assertSee($idea->created_at->diffForHumans())
        ->assertSee('https://example.com/one')
        ->assertSee('https://example.com/two')
        ->assertSee('target="_blank"', false)
        ->assertSee('rel="noopener noreferrer"', false);
});

test('the links section is hidden when an idea has no links', function (): void {
    // نتحقق من أن قسم الروابط لا يظهر عندما تكون مصفوفة JSON فارغة.
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create(['links' => []]);

    $this->actingAs($user)
        ->get(route('ideas.show', $idea))
        ->assertOk()
        ->assertDontSee('<h2 class="text-xl font-semibold">Links</h2>', false);
});

test('a user cannot view or delete another users idea', function (): void {
    // نمنع المستخدم الغريب من الوصول إلى فكرة لا يملكها أو حذفها.
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $idea = Idea::factory()->for($owner)->create();

    $this->actingAs($intruder)
        ->get(route('ideas.show', $idea))
        ->assertForbidden();

    $this->actingAs($intruder)
        ->delete(route('ideas.destroy', $idea))
        ->assertForbidden();

    expect(Idea::find($idea->id))->not->toBeNull();
});

test('the owner can delete an idea and returns to the ideas index', function (): void {
    // نتحقق من نجاح الحذف وإعادة التوجيه إلى قائمة الأفكار.
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create();

    $this->actingAs($user)
        ->delete(route('ideas.destroy', $idea))
        ->assertRedirect(route('ideas.index'));

    expect(Idea::find($idea->id))->toBeNull();
});
