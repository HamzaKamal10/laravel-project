<x-layout.layout>
    {{-- يشرح العنوان وظيفة الصفحة ويساعد المستخدم على فهم شبكة الأفكار. --}}
    <header class="flex flex-wrap items-end justify-between gap-4 pt-10">
        <div>
            <h1 class="text-4xl font-bold tracking-tight">Ideas</h1>
            <p class="mt-1 text-sm text-muted-foreground">Capture your thoughts. Make a plan.</p>
        </div>

        {{-- يفتح هذا الزر نموذج إنشاء فكرة جديدة. --}}
        <button type="button" x-data x-on:click="$dispatch('open-modal', 'create-idea')" class="btn">New Idea</button>
    </header>

        {{-- زر البطاقة يرسل حدث Alpine لفتح modal إنشاء الفكرة القابل لإعادة الاستخدام. --}}
        <x-card
            is="button"
            type="button"
            x-data
            x-on:click="$dispatch('open-modal', 'create-idea')"
            class="group mt-8 flex w-full cursor-pointer items-center justify-between border-dashed bg-card/80 text-left transition-colors hover:border-primary/50"
        >
            <span class="font-medium text-foreground">What's the idea?</span>
            <span class="text-2xl leading-none text-muted-foreground transition-colors group-hover:text-primary" aria-hidden="true">+</span>
        </x-card>

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
                    @if ($idea->image_path)
                        <div class="-mx-5 -mt-5 mb-4 overflow-hidden rounded-t-lg">
                            <img
                                src="{{ Storage::disk('public')->url($idea->image_path) }}"
                                alt="{{ $idea->title }}"
                                class="aspect-video w-full object-cover"
                            >
                        </div>
                    @endif
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

        {{-- نعرض modal الإنشاء قرب نهاية الصفحة حتى يبقى مستقلاً عن شبكة الأفكار. --}}
        <x-modal name="create-idea" title="New Idea">
            <form
                action="{{ route('ideas.store') }}"
                method="POST"
                enctype="multipart/form-data"
                class="mt-4 space-y-6"
                x-data="{ status: '{{ old('status', 'pending') }}', newLink: '', links: [], newStep: '', steps: [] }"
            >
                @csrf

                <x-form.field name="title" label="Title" autofocus />

                <x-form.field name="description" label="Description" as="textarea" rows="4" />

                {{-- حقل رفع الصورة المميزة --}}
                <div class="space-y-2">
                    <label for="image" class="block text-sm font-medium leading-none">Featured Image</label>
                    <input
                        id="image"
                        type="file"
                        name="image"
                        accept="image/*"
                        class="block w-full text-sm text-muted-foreground file:mr-4 file:rounded-md file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-medium file:text-primary-foreground hover:file:bg-primary/90 focus:outline-none"
                    >
                    <x-form.error name="image" />
                </div>

                {{-- حقول الخطوات العملية --}}
                <fieldset class="space-y-2">
                    <legend class="block text-sm font-medium leading-none">Actionable Steps</legend>

                    <div class="flex gap-2">
                        <input
                            type="text"
                            x-model="newStep"
                            placeholder="E.g., Research competitor pricing"
                            class="flex h-10 w-full rounded-md border border-border bg-transparent px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                            x-on:keydown.enter.prevent="if (newStep.trim()) { steps.push(newStep.trim()); newStep = ''; }"
                            aria-label="New actionable step"
                            data-test="new-step"
                        >
                        <button
                            type="button"
                            class="btn shrink-0"
                            x-on:click="if (newStep.trim()) { steps.push(newStep.trim()); newStep = ''; }"
                            :disabled="!newStep.trim()"
                            :class="{ 'opacity-50 cursor-not-allowed': !newStep.trim() }"
                            aria-label="Add step"
                            data-test="submit-new-step-button"
                        >
                            +
                        </button>
                    </div>

                    <template x-for="(step, index) in steps" :key="index">
                        <div class="flex gap-2">
                            <input
                                type="text"
                                name="steps[]"
                                :value="step"
                                readonly
                                class="flex h-10 w-full rounded-md border border-border bg-transparent px-3 py-2 text-sm text-foreground"
                            >
                            <button
                                type="button"
                                class="btn-outlined shrink-0 text-red-400 hover:border-red-400/50 hover:bg-red-400/10"
                                x-on:click="steps.splice(index, 1)"
                                :aria-label="'Remove step ' + step"
                                :data-test="'remove-step-' + index"
                            >
                                &times;
                            </button>
                        </div>
                    </template>

                    <x-form.error name="steps" />
                    <x-form.error name="steps.*" />
                </fieldset>

                {{-- حقول الروابط الديناميكية مدفوعة بـ Alpine لإضافة وإزالة روابط دون إعادة تحميل. --}}
                <fieldset class="space-y-2">
                    <legend class="block text-sm font-medium leading-none">Links</legend>

                    <div class="flex gap-2">
                        <input
                            type="url"
                            x-model="newLink"
                            placeholder="https://example.com"
                            class="flex h-10 w-full rounded-md border border-border bg-transparent px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                            x-on:keydown.enter.prevent="if (newLink.trim()) { links.push(newLink.trim()); newLink = ''; }"
                            aria-label="New link URL"
                            data-test="new-link"
                        >
                        <button
                            type="button"
                            class="btn shrink-0"
                            x-on:click="if (newLink.trim()) { links.push(newLink.trim()); newLink = ''; }"
                            :disabled="!newLink.trim()"
                            :class="{ 'opacity-50 cursor-not-allowed': !newLink.trim() }"
                            aria-label="Add link"
                            data-test="submit-new-link-button"
                        >
                            +
                        </button>
                    </div>

                    <template x-for="(link, index) in links" :key="index">
                        <div class="flex gap-2">
                            <input
                                type="url"
                                name="links[]"
                                :value="link"
                                readonly
                                class="flex h-10 w-full rounded-md border border-border bg-transparent px-3 py-2 text-sm text-foreground"
                            >
                            <button
                                type="button"
                                class="btn-outlined shrink-0 text-red-400 hover:border-red-400/50 hover:bg-red-400/10"
                                x-on:click="links.splice(index, 1)"
                                :aria-label="'Remove link ' + link"
                                :data-test="'remove-link-' + index"
                            >
                                &times;
                            </button>
                        </div>
                    </template>

                    <x-form.error name="links" />
                    <x-form.error name="links.*" />
                </fieldset>

                {{-- أزرار اختيار الحالة مدفوعة بـ Alpine لتحديث الحالة دون إرسال النموذج. --}}
                <div class="space-y-2">
                    <span class="block text-sm font-medium leading-none">Status</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach (\App\Enums\IdeaStatus::cases() as $statusCase)
                            <button
                                type="button"
                                x-on:click="status = '{{ $statusCase->value }}'"
                                :class="status === '{{ $statusCase->value }}' ? 'btn' : 'btn-outlined'"
                            >
                                {{ $statusCase->label() }}
                            </button>
                        @endforeach
                    </div>
                    <input type="hidden" name="status" :value="status">
                    <x-form.error name="status" />
                </div>

                {{-- أزرار الإلغاء والإنشاء توفر مساراً واضحاً للخروج أو الإرسال. --}}
                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="btn">Create Idea</button>
                    <button type="button" class="btn-outlined" x-on:click="$dispatch('close-modal')">Cancel</button>
                </div>
            </form>
        </x-modal>
</x-layout.layout>