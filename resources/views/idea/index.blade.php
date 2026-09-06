<x-layout.layout>
    {{-- يشرح العنوان وظيفة الصفحة ويساعد المستخدم على فهم شبكة الأفكار. --}}
    <header class="pt-10">
        <h1 class="text-3xl font-bold tracking-tight">Ideas</h1>
        <p class="mt-1 text-sm text-muted-foreground">Capture your thoughts. Make a plan.</p>
    </header>

    {{-- صف أزرار يتيح تصفية الأفكار حسب الحالة الحالية أو عرض جميع الأفكار. --}}
    <div class="mt-6 flex flex-wrap gap-3">
        {{-- يصبح زر All نشطاً عندما لا يوجد status في query string. --}}
        <a href="/ideas" class="btn {{ request('status') ? 'btn-outlined' : '' }}">
            All
            <span class="ml-1 text-xs opacity-75">{{ $statusCounts->get('all', 0) }}</span>
        </a>

        {{-- نولد زرًا لكل حالة من التعداد حتى تبقى الواجهة متزامنة مع الحالات المعرفة في التطبيق. --}}
        @foreach (\App\Enums\IdeaStatus::cases() as $status)
            <a
                href="/ideas?status={{ $status->value }}"
                class="btn {{ request('status') === $status->value ? '' : 'btn-outlined' }}"
            >
                {{ $status->label() }}
                <span class="ml-1 text-xs opacity-75">{{ $statusCounts->get($status->value, 0) }}</span>
            </a>
        @endforeach
    </div>

    {{-- تعرض الشبكة بطاقتين في الصف على الشاشات المتوسطة والأكبر. --}}
    <div class="mt-10 grid md:grid-cols-2 gap-6">
        @forelse($ideas as $idea)
            @php
                // نربط كل حالة بلون مناسب حتى تكون حالة الفكرة واضحة بصرياً.
                $statusClasses = match ($idea->status) {
                    \App\Enums\IdeaStatus::PENDING => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
                    \App\Enums\IdeaStatus::IN_PROGRESS => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                    \App\Enums\IdeaStatus::COMPLETED => 'bg-primary/10 text-primary border-primary/20',
                };
            @endphp

            {{-- البطاقة تجمع عنوان الفكرة ووصفها وبياناتها المختصرة في وحدة واحدة. --}}
            <x-card>
                {{-- يفتح العنوان صفحة التفاصيل الخاصة بهذه الفكرة. --}}
                <h2 class="text-lg font-bold">
                    <a href="{{ route('ideas.show', $idea) }}" class="hover:text-primary hover:underline">{{ $idea->title }}</a>
                </h2>
                <p class="mt-2 line-clamp-3 text-muted-foreground">{{ $idea->description }}</p>

                {{-- يوضح التذييل عمر الفكرة وحالتها الحالية. --}}
                <footer class="mt-6 flex items-center justify-between gap-4">
                    <time class="text-xs text-muted-foreground">{{ $idea->created_at->diffForHumans() }}</time>
                    <span class="inline-block rounded-full border px-2 py-1 text-xs font-medium {{ $statusClasses }}">
                        {{ $idea->status->label() }}
                    </span>
                </footer>
            </x-card>
        @empty
            {{-- تظهر هذه الرسالة عندما لا يملك المستخدم أي أفكار بعد. --}}
            <p class="text-muted-foreground">No ideas at this time.</p>
        @endforelse
    </div>
</x-layout.layout>