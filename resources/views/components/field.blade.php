@props([
    'name',
    'label',
    'type' => 'text',
])

<div class="min-w-0">
    <label for="{{ $name }}" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-zinc-500">
        {{ $label }}
    </label>
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ request($name) }}"
        class="block h-9 w-full border border-zinc-700 bg-zinc-950 px-2.5 text-xs text-zinc-200 outline-none transition placeholder:text-zinc-700 focus:border-zinc-500 focus:ring-1 focus:ring-zinc-500"
    >
</div>
