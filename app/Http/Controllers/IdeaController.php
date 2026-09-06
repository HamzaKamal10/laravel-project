<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\IdeaStatus;
use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use App\Models\Idea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class IdeaController extends Controller
{
    // يعرض نموذج إنشاء فكرة جديدة للمستخدم المسجل دخوله.
    public function create(): View
    {
        return view('idea.create');
    }

    // يجلب أفكار المستخدم الحالي فقط عبر علاقة User لتجنب عرض أفكار الآخرين.
    public function index(): View
    {
        // نستخرج القيم المسموح بها من التعداد لمنع استخدام حالة غير معروفة.
        $validStatuses = collect(IdeaStatus::cases())->pluck('value')->all();
        $requestedStatus = request('status');
        $status = in_array($requestedStatus, $validStatuses, true) ? $requestedStatus : null;

        $ideas = auth()->user()->ideas()
            // نضيف شرط الحالة فقط إذا كانت قيمة status صالحة.
            ->when($status, function (Builder $query, string $status): Builder {
                return $query->where('status', $status);
            })
            ->get();

        // نستدعي منطق العد من النموذج ونمرر النتيجة إلى واجهة الأفكار.
        $statusCounts = Idea::statusCounts(auth()->user());

        return view('idea.index', compact('ideas', 'statusCounts'));
    }

    public function store(StoreIdeaRequest $request)
    {
        $data = $request->validated();
        // نربط الفكرة بالمستخدم الحالي حتى لا تصبح بلا مالك أو تظهر لمستخدم آخر.
        $data['user_id'] = auth()->id();

        Idea::create($data);

        return redirect()->route('ideas.index')->with('success', 'Idea created.');
    }

    // يعرض تفاصيل الفكرة بعد التأكد من أن المستخدم يملك صلاحية رؤيتها.
    public function show(Idea $idea): View
    {
        Gate::authorize('view', $idea);

        return view('idea.show', compact('idea'));
    }

    public function update(UpdateIdeaRequest $request, Idea $idea)
    {
        $idea->update($request->validated());

        return response()->json($idea);
    }

    // يحذف الفكرة المطلوبة فقط بعد تطبيق سياسة الملكية ثم يعيد المستخدم للفهرس.
    public function destroy(Idea $idea)
    {
        Gate::authorize('delete', $idea);
        $idea->delete();

        return redirect()->route('ideas.index')->with('success', 'Idea deleted.');
    }
}
