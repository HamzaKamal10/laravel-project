{{-- مكوّن بطاقة عام يعيد استخدام نفس الإطار والمسافات مع أي محتوى داخل slot. --}}
<div {{ $attributes->merge(['class' => 'border border-border rounded-lg bg-card p-4 text-sm']) }}>
    {{-- يعرض slot المحتوى الذي تمرره الصفحة إلى البطاقة. --}}
    {{ $slot }}
</div>