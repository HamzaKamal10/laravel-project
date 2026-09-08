@props(['idea' => null, 'name' => 'create-idea'])

<x-modal :name="$name" :title="$idea ? 'Edit Idea' : 'New Idea'">
    <form
        action="{{ $idea ? route('ideas.update', $idea) : route('ideas.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="mt-4 space-y-6"
        x-data="{ 
            status: '{{ old('status', $idea?->status?->value ?? 'pending') }}', 
            newLink: '', 
            links: @js(old('links', $idea?->links ?? [])), 
            newStep: '', 
            steps: @js(old('steps', $idea?->steps ? $idea->steps->pluck('description')->all() : []))
        }"
    >
        @csrf
        @if ($idea)
            @method('PATCH')
        @endif

        <x-form.field name="title" label="Title" :value="$idea?->title" autofocus />

        <x-form.field name="description" label="Description" as="textarea" rows="4" :value="$idea?->description" />

        @if($idea && $idea->image_path)
            <div class="space-y-2">
                <label class="block text-sm font-medium leading-none">Current Image</label>
                <div class="relative w-full max-w-sm">
                    <img src="{{ Storage::disk('public')->url($idea->image_path) }}" alt="{{ $idea->title }}" class="rounded-md border border-border">
                </div>
            </div>
        @endif

        {{-- حقل رفع الصورة المميزة --}}
        <div class="space-y-2">
            <label for="image" class="block text-sm font-medium leading-none">{{ $idea && $idea->image_path ? 'Replace Image' : 'Featured Image' }}</label>
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
            <button type="submit" class="btn">{{ $idea ? 'Update Idea' : 'Create Idea' }}</button>
            <button type="button" class="btn-outlined" x-on:click="$dispatch('close-modal')">Cancel</button>
        </div>
    </form>
</x-modal>
