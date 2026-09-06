<x-layout.layout>
    {{-- يشرح العنوان وظيفة الصفحة ويساعد المستخدم على فهم شبكة الأفكار. --}}
    <header class="flex flex-wrap items-end justify-between gap-4 pt-10">
        <div>
            <h1 class="text-4xl font-bold tracking-tight">Ideas</h1>
            <p class="mt-1 text-sm text-muted-foreground">Capture your thoughts. Make a plan.</p>
        </div>

        {{-- يفتح هذا الزر نموذج إنشاء فكرة جديدة. --}}
        <a href="{{ route('ideas.create') }}" class="btn">New Idea</a>
    </header>

    {{-- بطاقة دخول سريعة لنفس نموذج إنشاء الفكرة الموجود في التطبيق. --}}
    <a href="{{ route('ideas.create') }}" class="group mt-8 block rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background">
        <x-card class="flex items-center justify-between border-dashed bg-card/80 transition-colors group-hover:border-primary/50">
            <span class="font-medium text-foreground">What's the idea?</span>
            <span class="text-2xl leading-none text-muted-foreground transition-colors group-hover:text-primary" aria-hidden="true">+</span>
        </x-card>
    </a>

    {{-- صف أزرار يتيح تصفية الأفكار حسب الحالة الحالية أو عرض جميع الأفكار. --}}
    <div class="mt-10 flex flex-wrap gap-2">
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
    <div class="mt-10 grid gap-6 md:grid-cols-2">
        @forelse($ideas as $idea)
            @php
                // نربط كل حالة بلون مناسب حتى تكون حالة الفكرة واضحة بصرياً.
                $statusClasses = match ($idea->status) {
                    \App\Enums\IdeaStatus::PENDING => 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
                    \App\Enums\IdeaStatus::IN_PROGRESS => 'bg-blue-500/10 text-blue-500 border-blue-500/20',
                    \App\Enums\IdeaStatus::COMPLETED => 'bg-primary/10 text-primary border-primary/20',
                };
            @endphp

            {{-- البطاقة كلها رابط حتى تكون الفكرة قابلة للنقر من أي مساحة واضحة فيها. --}}
            <a href="{{ route('ideas.show', $idea) }}" class="group block rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2">
                    <x-card class="h-full group-hover:border-primary/60">
                    <h2 class="text-lg font-bold transition-colors group-hover:text-primary">{{ $idea->title }}</h2>
                    <p class="mt-2 line-clamp-3 text-muted-foreground">{{ $idea->description }}</p>

                    {{-- يوضح التذييل عمر الفكرة وحالتها الحالية. --}}
                    <footer class="mt-6 flex flex-wrap items-center justify-between gap-3">
                        <time class="text-xs text-muted-foreground">{{ $idea->created_at->diffForHumans() }}</time>
                        <span class="inline-block rounded-full border px-2 py-1 text-xs font-medium {{ $statusClasses }}">
                            {{ $idea->status->label() }}
                        </span>
                    </footer>
                </x-card>
            </a>
        @empty
            {{-- تعرض بطاقة هادئة الحالة الفارغة مع إبقاء زر الإنشاء متاحاً في أعلى الصفحة. --}}
            <x-card class="col-span-full text-center">
                <p class="text-muted-foreground">No ideas at this time.</p>
            </x-card>
        @endforelse
    </div>
</x-layout.layout>