<?php

declare(strict_types=1);

use App\Http\Controllers\IdeaController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionsController;
use Illuminate\Support\Facades\Route;

// ينقل المسار الجذري المستخدم إلى صفحة أفكاره المخصصة.
Route::redirect('/', '/ideas');
// يحمي هذا المسار بيانات الأفكار بحيث يرى المستخدم أفكاره بعد تسجيل الدخول فقط.
Route::get('/ideas', [IdeaController::class, 'index'])
    ->middleware('auth')
    ->name('ideas.index');
Route::get('/ideas/{idea}', [IdeaController::class, 'show'])
    ->middleware('auth')
    ->name('ideas.show');
Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy'])
    ->middleware('auth')
    ->name('ideas.destroy');
Route::post('/ideas', [IdeaController::class, 'store']);

// تعرض هذه المسارات نموذج التسجيل وتتحقق من بيانات الحساب الجديد.
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// يعرض هذا المسار نموذج تسجيل الدخول.
Route::get('/login', [SessionsController::class, 'create'])->name('login');
Route::post('/login', [SessionsController::class, 'store']);
