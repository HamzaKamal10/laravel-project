<?php

it('registers a user', function (): void {
    // نزور صفحة التسجيل ونملأ الحقول ثم نتحقق من الانتقال بعد إنشاء الحساب.
    visit('/register')
        ->fill('name', 'John Doe')
        ->fill('email', 'john@example.com')
        ->fill('password', 'password')
        ->click('Create Account')
        ->assertPathIs('/');
});

it('requires a valid email', function (): void {
    // نرسل بريداً غير صالح، لذلك يجب أن يعيدنا Laravel إلى نموذج التسجيل.
    visit('/register')
        ->fill('name', 'John Doe')
        ->fill('email', 'john123')
        ->fill('password', 'password')
        ->click('Create Account')
        ->assertPathIs('/register');
});
