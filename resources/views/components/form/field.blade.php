@props(['name', 'label', 'type' => 'text', 'as' => 'input'])

<div class="space-y-2">
    <label for="{{ $name }}" class="block text-sm font-medium leading-none">{{ $label }}</label>

    @if ($as === 'textarea')
        <textarea
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $attributes->merge(['class' => 'block w-full rounded-md border border-border bg-transparent px-3 py-2 text-sm text-foreground outline-none focus:border-primary focus:ring-2 focus:ring-primary', 'rows' => '4']) }}
        >{{ old($name) }}</textarea>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name) }}"
            {{ $attributes->merge(['class' => 'flex h-10 w-full rounded-md border border-border bg-transparent px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary']) }}
        >
    @endif

    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>