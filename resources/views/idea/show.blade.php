<x-layout.layout>
    @php
        // نستخدم نفس ألوان الحالة الموجودة في بطاقات الفهرس للحفاظ على الاتساق البصري.
        $statusClasses = match ($idea->status) {
            \App\Enums\IdeaStatus::PENDING => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
            \App\Enums\IdeaStatus::IN_PROGRESS => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
            \App\Enums\IdeaStatus::COMPLETED => 'bg-primary/10 text-primary border-primary/20',
        };
    @endphp

    <div class="mx-auto max-w-3xl space-y-8 py-10">
        {{-- يضم الصف رابط العودة إلى الفهرس وأزرار إجراءات الفكرة. --}}
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('ideas.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline">
                {{-- سهم رجوع بسيط لأن المشروع لا يحتوي على مكوّن أيقونات جاهز. --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                Back to ideas
            </a>

            <div class="flex items-center gap-3">
                {{-- زر العرض فقط لأن مسار التعديل مؤجل في هذه المرحلة. --}}
                <button type="button" class="btn-outlined inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium">
                    {{-- أيقونة تعديل خارجية توضح الإجراء المؤجل بصرياً. --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M14 3h7v7" />
                        <path d="M10 14 21 3" />
                        <path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5" />
                    </svg>
                    Edit Idea
                </button>

                {{-- يرسل النموذج طلب DELETE الحقيقي مع حماية CSRF. --}}
                <form method="POST" action="{{ route('ideas.destroy', $idea) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-md px-4 py-2 text-sm font-medium text-red-400 transition-colors hover:bg-red-400/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-400 focus-visible:ring-offset-2 focus-visible:ring-offset-background">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        {{-- يعرض عنوان الفكرة وحالتها وتاريخ إنشائها بشكل بارز. --}}
        <header>
            <h1 class="text-4xl font-bold tracking-tight">{{ $idea->title }}</h1>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <span class="inline-block rounded-full border px-2 py-1 text-xs font-medium {{ $statusClasses }}">
                    {{ $idea->status->label() }}
                </span>
                <time class="text-sm text-muted-foreground">{{ $idea->created_at->diffForHumans() }}</time>
            </div>
        </header>

        {{-- البطاقة تعزل وصف الفكرة عن بقية تفاصيل الصفحة. --}}
        <x-card>
            <p class="whitespace-pre-line leading-7 text-muted-foreground">{{ $idea->description }}</p>
        </x-card>

        {{-- لا يظهر قسم الروابط إذا كانت مصفوفة JSON فارغة. --}}
        @if ($idea->links && count($idea->links) > 0)
            <section class="space-y-4">
                <h2 class="text-xl font-semibold">Links</h2>

                <div class="space-y-3">
                    @foreach ($idea->links as $link)
                        {{-- كل رابط يفتح في تبويب جديد مع حماية opener. --}}
                        <x-card class="transition-colors hover:border-primary/60">
                            <a href="{{ $link }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between gap-3 text-primary hover:underline">
                                <span class="break-all">{{ $link }}</span>
                                {{-- أيقونة رابط خارجي صغيرة توضح سلوك الفتح في تبويب جديد. --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M15 3h6v6" />
                                    <path d="M10 14 21 3" />
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                </svg>
                            </a>
                        </x-card>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-layout.layout>