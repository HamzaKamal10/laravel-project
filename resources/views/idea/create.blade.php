<x-layout.layout>
    <div class="mx-auto max-w-2xl py-10">
        {{-- يوضح عنوان الصفحة للمستخدم أنه ينشئ فكرة جديدة. --}}
        <header>
            <h1 class="text-3xl font-bold tracking-tight">Create an idea</h1>
            <p class="mt-1 text-sm text-muted-foreground">Capture a thought and turn it into a plan.</p>
        </header>

        {{-- بطاقة النموذج تجمع حقول الفكرة وتحافظ على لغة التصميم الحالية. --}}
        <x-card class="mt-8">
            <form action="{{ route('ideas.store') }}" method="POST" class="space-y-6">
                @csrf

                <x-form.field name="title" label="Title" />

                <div class="space-y-2">
                    <label for="description" class="block text-sm font-medium leading-none">Description</label>
                    <textarea name="description" id="description" rows="6" class="block w-full rounded-md border border-border bg-transparent px-3 py-2 text-sm text-foreground outline-none focus:border-primary focus:ring-2 focus:ring-primary" required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- أزرار الحفظ والعودة توفران مساراً واضحاً للخروج من النموذج. --}}
                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="btn">Create Idea</button>
                    <a href="{{ route('ideas.index') }}" class="btn-outlined">Cancel</a>
                </div>
            </form>
        </x-card>
    </div>
</x-layout.layout>