<?php

it('logs in a user', function (): void {
    $email = 'login-'.uniqid().'@example.com';

    // ننشئ الحساب من المتصفح أولاً حتى تكون البيانات مرئية لخادم Browser نفسه.
    visit('/register')
        ->fill('name', 'Login User')
        ->fill('email', $email)
        ->fill('password', 'password')
        ->click('Create Account');

    // نفتح نموذج الدخول بعد إنشاء الحساب ثم نتحقق من الوصول للأفكار.
    visit('/login')
        ->fill('email', $email)
        ->fill('password', 'password')
        ->click('Sign In')
        ->assertPathIs('/ideas')
        ->assertSee('Ideas');
});

it('logs out a user', function (): void {
    $email = 'logout-'.uniqid().'@example.com';

    // ننشئ مستخدماً عبر واجهة التسجيل ثم نضغط زر الخروج من الجلسة الحالية.
    visit('/register')
        ->fill('name', 'Logout User')
        ->fill('email', $email)
        ->fill('password', 'password')
        ->click('Create Account')
        // نضغط زر الخروج بعد اكتمال التسجيل ثم نتحقق من العودة لنموذج الدخول.
        ->click('Log Out')
        ->assertPathIs('/login')
        ->assertSee('Log in');
});
