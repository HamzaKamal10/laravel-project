<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Idea;
use Illuminate\Http\Request;

class IdeaController extends Controller
{
    public function store(Request $request)
    {
        // 1. التحقق من صحة البيانات المدخلة
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'required',
        ]);

        // 2. حفظ الفكرة في قاعدة البيانات
        Idea::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'status' => 'Pending',
        ]);

        // 3. إعادة توجيه المستخدم للصفحة الرئيسية
        return redirect('/');
    }
}
