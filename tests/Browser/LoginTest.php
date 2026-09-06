<?php

use App\Models\User;

it('logs in a user', function (): void {
    // ننشئ مستخدماً بكلمة المرور التي سيستخدمها اختبار المتصفح.
    $user = User::factory()->create([
        'password' => 'password',
    ]);

    // نملأ نموذج الدخول ثم نتحقق من إنشاء جلسة مصادقة للمستخدم.
    visit('/login')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('Sign In');

    $this->assertAuthenticated();
});

it('logs out a user', function (): void {
    // نجهز مستخدماً مسجلاً دخوله قبل زيارة الصفحة الرئيسية.
    $user = User::factory()->create();
    $this->actingAs($user);

    // نضغط على رابط تسجيل الخروج ثم نتحقق من انتهاء جلسة المستخدم.
    visit('/')
        ->click('Log Out');

    $this->assertGuest();
});
