<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\IdeaStatus;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Idea extends Model
{
    use HasFactory;

    protected $attributes = [
        // استخدام القيمة النصية يمنع أخطاء التحويل عند حفظ الحالة في قاعدة البيانات.
        'status' => IdeaStatus::PENDING->value,
    ];

    protected $casts = [
        // يحافظ هذا التحويل على الروابط كمصفوفة قابلة للتعامل معها داخل النموذج.
        'links' => AsArrayObject::class,
        // يحوّل قيمة الحالة النصية إلى قيمة من التعداد IdeaStatus.
        'status' => IdeaStatus::class,
    ];

    // يحسب أعداد كل حالات أفكار المستخدم ويضمن ظهور الحالات التي عددها صفر.
    public static function statusCounts(User $user): Collection
    {
        $countsByStatus = $user->ideas()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // نضع صفراً للحالات غير الموجودة ونضيف العدد الإجمالي لكل أفكار المستخدم.
        return collect(IdeaStatus::cases())
            ->mapWithKeys(function (IdeaStatus $status) use ($countsByStatus): array {
                return [$status->value => $countsByStatus->get($status->value, 0)];
            })
            ->put('all', $user->ideas()->count());
    }

    // كل فكرة تنتمي إلى مستخدم واحد، والمفتاح الأجنبي موجود في جدول ideas.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // يمكن للفكرة أن تمتلك عدة خطوات، ويرتبط كل Step بهذه الفكرة.
    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }
}
