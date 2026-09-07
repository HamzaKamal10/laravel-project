{{-- مكوّن modal قابل لإعادة الاستخدام، ويفتح فقط عندما يطابق اسم الحدث اسم المكوّن. --}}
@props([
    'name',
    'title',
])

<div
    x-data="{ show: false }"
    x-on:open-modal.window="show = $event.detail === '{{ $name }}'"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    x-cloak
    style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto bg-black/70 p-4 backdrop-blur-sm"
    role="dialog"
    aria-modal="true"
    :aria-hidden="(!show).toString()"
    aria-labelledby="modal-{{ $name }}-title"
    tabindex="-1"
>
    {{-- تنسق هذه اللوحة محتوى modal في المنتصف باستخدام لغة البطاقة الحالية. --}}
    <div
        class="flex min-h-full items-center justify-center"
        x-on:click.outside="show = false"
    >
        <x-card
            is="div"
            class="w-full max-w-lg transform transition duration-200 ease-out motion-safe:translate-y-0 motion-safe:scale-100 motion-safe:opacity-100"
            x-show="show"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95 sm:translate-y-0"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95 sm:translate-y-0"
            tabindex="-1"
        >
            {{-- يربط العنوان بالـ dialog عبر aria-labelledby لتحسين الوصول. --}}
            <h2 id="modal-{{ $name }}-title" class="text-xl font-semibold text-foreground">
                {{ $title }}
            </h2>

            <div class="mt-4">
                {{ $slot }}
            </div>
        </x-card>
    </div>
</div>
