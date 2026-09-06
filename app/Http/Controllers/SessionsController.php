<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SessionsController extends Controller
{
    // يعرض نموذج تسجيل الدخول، بينما تتم إضافة منطق الجلسة لاحقاً.
    public function create(): View
    {
        return view('auth.login');
    }

    // يتحقق من بيانات الدخول، ثم ينشئ جلسة للمستخدم ويعيده مع رسالة نجاح.
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            // تعاد الأخطاء إلى نموذج الدخول دون إنشاء جلسة عند فشل البيانات.
            return back()->withErrors([
                'email' => 'The provided credentials are incorrect.',
            ])->onlyInput('email');
        }

        // نغير معرّف الجلسة بعد الدخول للحماية من تثبيت الجلسة.
        $request->session()->regenerate();

        return redirect('/')->with('success', 'You are now logged in.');
    }
}
