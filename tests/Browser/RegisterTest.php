<?php

it('registers a user', function (): void {
    $email = 'john-'.uniqid().'@example.com';

    // نزور صفحة التسجيل ونملأ الحقول ثم نتحقق من الانتقال بعد إنشاء الحساب.
    visit('/register')
        ->fill('name', 'John Doe')
        ->fill('email', $email)
        ->fill('password', 'password')
        ->click('Create Account')
        ->assertPathIs('/ideas')
        ->assertSee('Ideas');
});

it('requires a valid email', function (): void {
    $email = 'invalid-'.uniqid();

    // نرسل بريداً غير صالح، لذلك يجب أن يعيدنا Laravel إلى نموذج التسجيل.
    visit('/register')
        ->fill('name', 'John Doe')
        ->fill('email', $email)
        ->fill('password', 'password')
        ->click('Create Account')
        ->assertPathIs('/register');
});
