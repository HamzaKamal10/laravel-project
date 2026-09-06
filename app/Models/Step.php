<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Step extends Model
{
    use HasFactory;

    // تبدأ كل خطوة بحالة غير مكتملة إلى أن ينجزها المستخدم.
    protected $attributes = [
        'completed' => false,
    ];

    // علاقة ينتمي فيها كل Step إلى Idea واحدة عبر idea_id.
    public function idea(): BelongsTo
    {
        return $this->belongsTo(Idea::class);
    }
}
