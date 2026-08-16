@php
    $routePrefix = rtrim((string) config('model-audit.ui.route_name_prefix', 'model-audit.'), '.') . '.';
@endphp

<x-dynamic-component
    :component="config('model-audit.ui.layout', 'model-audit::layout')"
    :title="__('model-audit::ui.subject_title', ['type' => $query->subjectType, 'id' => $query->subjectId])"
>
    <x-model-audit::page-header
        :title="$query->subjectType . ' [' . $query->subjectId . ']'"
        :back-url="route($routePrefix . 'index')"
        mono
    />

    <div class="grid gap-4">
        <x-model-audit::integrity :result="$integrity" />

        <x-model-audit::panel>
            @if ($entries->isEmpty())
                <x-model-audit::empty-state />
            @else
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-zinc-800 bg-zinc-900 text-xs font-semibold uppercase tracking-widest text-zinc-500">
                                <th class="px-4 py-3">{{ __('model-audit::ui.event') }}</th>
                                <th class="px-4 py-3">UUID</th>
                                <th class="px-4 py-3">{{ __('model-audit::ui.actor') }}</th>
                                <th class="px-4 py-3">{{ __('model-audit::ui.created_at') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800 text-xs text-zinc-400">
                            @foreach ($entries as $entry)
                                <tr class="transition hover:bg-zinc-900/70">
                                    <td class="px-4 py-3"><a href="{{ route($routePrefix . 'show', $entry->uuid) }}" class="hover:text-white"><x-model-audit::event :event="$entry->event" /></a></td>
                                    <td class="px-4 py-3"><a href="{{ route($routePrefix . 'show', $entry->uuid) }}" class="font-mono text-zinc-300 hover:text-white hover:underline">{{ $entry->uuid }}</a></td>
                                    <td class="px-4 py-3">{{ $entry->actor_type !== null && $entry->actor_id !== null ? $entry->actor_type . ' [' . $entry->actor_id . ']' : __('model-audit::ui.system') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3"><time datetime="{{ $entry->created_at?->toIso8601String() }}">{{ $entry->created_at?->translatedFormat('d M Y, H:i:s') }}</time></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-model-audit::panel>
    </div>
</x-dynamic-component>
