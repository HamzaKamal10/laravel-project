<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    // يعرض نموذج إنشاء حساب جديد للمستخدم.
    public function create(): View
    {
        return view('auth.register');
    }

    // يتحقق من البيانات ثم ينشئ مستخدماً جديداً مع تشفير كلمة المرور.
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // نعيد المستخدم إلى الصفحة الرئيسية مع رسالة نجاح مؤقتة لعرضها في الواجهة.
        return redirect('/')->with('success', 'Registration complete!');
    }
}
