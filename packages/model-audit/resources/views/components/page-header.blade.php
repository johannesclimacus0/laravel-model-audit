@props([
    'title',
    'backUrl' => null,
    'mono' => false,
])

<header class="mb-6 flex flex-col gap-4 border-b border-zinc-800 pb-4 sm:flex-row sm:items-end sm:justify-between">
    <div class="min-w-0">
        <h1 @class([
            'truncate text-xl font-semibold tracking-tight text-zinc-100',
            'font-mono text-base sm:text-lg' => $mono,
        ])>{{ $title }}</h1>

        @isset($meta)
            <div class="mt-3">{{ $meta }}</div>
        @endisset
    </div>

    @if ($backUrl)
        <a href="{{ $backUrl }}" class="inline-flex h-9 shrink-0 items-center justify-center border border-zinc-700 bg-zinc-900 px-3 text-xs font-semibold text-zinc-300 transition hover:border-zinc-500 hover:bg-zinc-800 hover:text-white">
            {{ __('model-audit::ui.back_to_log') }}
        </a>
    @endif
</header>
