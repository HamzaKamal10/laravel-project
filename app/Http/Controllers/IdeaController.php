<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\UpdateIdea;
use App\Enums\IdeaStatus;
use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use App\Models\Idea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
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
            ->latest()
            ->get();

        // نستدعي منطق العد من النموذج ونمرر النتيجة إلى واجهة الأفكار.
        $statusCounts = Idea::statusCounts(auth()->user());

        return view('idea.index', compact('ideas', 'statusCounts'));
    }

    public function store(StoreIdeaRequest $request)
    {
        $imagePath = null;

        try {
            DB::transaction(function () use ($request, &$imagePath) {
                // نربط الفكرة بالمستخدم الحالي عبر العلاقة حتى لا تصبح بلا مالك أو تظهر لمستخدم آخر.
                $idea = auth()->user()->ideas()->create($request->safe()->except(['steps', 'image']));

                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('ideas', 'public');
                    $idea->update(['image_path' => $imagePath]);
                }

                if ($request->has('steps')) {
                    $steps = collect($request->input('steps'))->map(fn (array $step) => [
                        'description' => $step['description'],
                        'completed' => (bool) ($step['completed'] ?? false),
                    ])->all();

                    $idea->steps()->createMany($steps);
                }
            });
        } catch (\Throwable $e) {
            // ننظف الملف اليتيم إذا فشلت المعاملة بعد تخزين الصورة.
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $e;
        }

        return redirect()->route('ideas.index')->with('success', 'Idea created.');
    }

    // يعرض تفاصيل الفكرة بعد التأكد من أن المستخدم يملك صلاحية رؤيتها.
    public function show(Idea $idea): View
    {
        Gate::authorize('view', $idea);

        $idea->load('steps');

        return view('idea.show', compact('idea'));
    }

    public function update(UpdateIdeaRequest $request, Idea $idea, UpdateIdea $updateIdea)
    {
        Gate::authorize('update', $idea);

        $updateIdea->handle($request->safe()->all(), $idea);

        return redirect()->route('ideas.show', $idea)->with('success', 'Idea updated.');
    }

    // يحذف الفكرة المطلوبة فقط بعد تطبيق سياسة الملكية ثم يعيد المستخدم للفهرس.
    public function destroy(Idea $idea)
    {
        Gate::authorize('delete', $idea);
        $idea->delete();

        return redirect()->route('ideas.index')->with('success', 'Idea deleted.');
    }
}
