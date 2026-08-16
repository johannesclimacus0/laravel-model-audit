@props([
    'title' => null,
])

<div {{ $attributes->class('min-w-0 border border-zinc-800 bg-zinc-900/40') }}>
    @if ($title)
        <h2 class="border-b border-zinc-800 bg-zinc-900 px-4 py-3 text-xs font-semibold uppercase tracking-widest text-zinc-500">
            {{ $title }}
        </h2>
    @endif

    {{ $slot }}
</div>
