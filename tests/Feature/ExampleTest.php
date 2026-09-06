<?php

test('the application returns a successful response', function () {
    // نتحقق من أن المسار الجذري يعيد المستخدم إلى صفحة الأفكار الجديدة.
    $response = $this->get('/');

    $response->assertRedirect('/ideas');
});
