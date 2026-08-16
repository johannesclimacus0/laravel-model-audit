@props(['label'])

<div class="grid gap-1 border-b border-zinc-800 px-4 py-3 last:border-b-0 sm:grid-cols-4 sm:gap-4">
    <dt class="text-xs text-zinc-500">{{ $label }}</dt>
    <dd {{ $attributes->class('min-w-0 break-words text-xs text-zinc-300 sm:col-span-3') }}>{{ $slot }}</dd>
</div>
