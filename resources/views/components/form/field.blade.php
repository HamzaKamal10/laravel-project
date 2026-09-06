@props(['name', 'label', 'type' => 'text'])

<div class="space-y-2">
    <label for="{{ $name }}" class="block text-sm font-medium leading-none">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" class="flex h-10 w-full rounded-md border border-border bg-transparent px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
</div>