{{-- بطاقة مشتركة تمنح المحتوى حدوداً ومسافات وتسلسلاً بصرياً ثابتاً. --}}
<div {{ $attributes->merge(['class' => 'rounded-lg border border-border bg-card p-5 text-sm shadow-sm shadow-black/20 transition-all hover:border-white/20 hover:shadow-lg hover:shadow-black/30']) }}>
    {{-- يعرض slot المحتوى الذي تمرره الصفحة إلى البطاقة. --}}
    {{ $slot }}
</div>