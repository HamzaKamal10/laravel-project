<?php

use App\Models\Idea;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إنشاء جدول الخطوات المرتبطة بالأفكار.
     */
    public function up(): void
    {
        Schema::create('steps', function (Blueprint $table) {
            // معرّف فريد لكل خطوة.
            $table->id();
            // مفتاح أجنبي يربط الخطوة بفكرة، والحذف المتسلسل يحذف خطوات الفكرة معها.
            $table->foreignIdFor(Idea::class)->constrained()->cascadeOnDelete();
            // وصف العمل المطلوب تنفيذه في هذه الخطوة.
            $table->string('description');
            // يحدد إن كانت الخطوة مكتملة، وتبدأ افتراضياً بأنها غير مكتملة.
            $table->boolean('completed')->default(false);
            // تاريخا الإنشاء والتحديث اللذان يديرهما Laravel تلقائياً.
            $table->timestamps();
        });
    }

    /**
     * حذف جدول الخطوات عند التراجع عن الهجرة.
     */
    public function down(): void
    {
        Schema::dropIfExists('steps');
    }
};
