<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('ideas', function (Blueprint $table) {
        $table->id(); // المعرف الفريد للفكرة
        $table->string('title'); // عنوان الفكرة (نص قصير)
        $table->text('description'); // تفاصيل الفكرة (نص طويل)
        $table->string('status')->default('Pending'); // حالة الفكرة (الافتراضي: قيد الانتظار)
        $table->timestamps(); // تاريخ الإنشاء والتحديث
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ideas');
    }
};
