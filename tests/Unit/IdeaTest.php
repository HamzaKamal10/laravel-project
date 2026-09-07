<?php

use App\Enums\IdeaStatus;
use App\Models\Idea;
use App\Models\User;

test('it belongs to a user', function (): void {
    // ينشئ المصنع فكرة مع مستخدم مرتبط، ثم نتحقق من علاقة belongsTo.
    $idea = Idea::factory()->create();

    expect($idea->user)->toBeInstanceOf(User::class);
});

test('it can have steps', function (): void {
    // نبدأ بفكرة جديدة ونتأكد أن مجموعة خطواتها فارغة.
    $idea = Idea::factory()->create();

    expect($idea->steps)->toBeEmpty();

    // تنشئ العلاقة خطوة جديدة وتملأ المفتاح idea_id تلقائياً.
    $idea->steps()->create([
        'description' => 'Complete the first step',
    ]);

    // نعيد تحميل الفكرة للتأكد من قراءة الخطوة المحفوظة من قاعدة البيانات.
    expect($idea->fresh()->steps)->toHaveCount(1);
});

test('seeded ideas use valid enum status values', function (): void {
    $this->artisan('db:seed', ['--class' => 'DatabaseSeeder'])->assertOk();

    $ideas = Idea::query()->get();

    expect($ideas)->not->toBeEmpty();

    foreach ($ideas as $idea) {
        expect($idea->status)->toBeInstanceOf(IdeaStatus::class);
        expect($idea->status->value)->toBeIn([
            IdeaStatus::PENDING->value,
            IdeaStatus::IN_PROGRESS->value,
            IdeaStatus::COMPLETED->value,
        ]);
    }
});
