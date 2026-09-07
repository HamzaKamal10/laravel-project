{{-- يعرض رسالة الخطأ إن وُجدت للحقل المحدد بالاسم. --}}
@props(['name'])

@error($name)
    <p class="text-sm text-red-600">{{ $message }}</p>
@enderror
