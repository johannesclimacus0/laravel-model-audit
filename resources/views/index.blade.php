@php
    $routePrefix = rtrim((string) config('model-audit.ui.route_name_prefix', 'model-audit.'), '.') . '.';
@endphp

<x-dynamic-component
    :component="config('model-audit.ui.layout', 'model-audit::layout')"
    :title="__('model-audit::ui.title')"
>
    <x-model-audit::page-header :title="__('model-audit::ui.title')" />

    @if ($errors->any())
        <div class="mb-4 border border-red-800 bg-red-900/40 px-4 py-3 text-xs text-red-300">
            {{ $errors->first() }}
        </div>
    @endif

    <x-model-audit::panel class="mb-4">
        <form method="GET" action="{{ route($routePrefix . 'index') }}" class="grid gap-3 p-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                'event' => 'event',
                'subject_type' => 'subject_type',
                'subject_id' => 'subject_id',
                'actor_type' => 'actor_type',
                'actor_id' => 'actor_id',
                'request_id' => 'request_id',
            ] as $name => $label)
                <x-model-audit::field :name="$name" :label="__('model-audit::ui.' . $label)" />
            @endforeach

            <x-model-audit::field name="date_from" type="date" :label="__('model-audit::ui.date_from')" />
            <x-model-audit::field name="date_to" type="date" :label="__('model-audit::ui.date_to')" />

            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex h-9 items-center border border-zinc-600 bg-zinc-800 px-3 text-xs font-semibold text-zinc-100 transition hover:border-zinc-500 hover:bg-zinc-700">
                    {{ __('model-audit::ui.apply_filters') }}
                </button>
                <a href="{{ route($routePrefix . 'index') }}" class="inline-flex h-9 items-center border border-zinc-700 px-3 text-xs font-semibold text-zinc-400 transition hover:border-zinc-500 hover:text-white">
                    {{ __('model-audit::ui.reset_filters') }}
                </a>
            </div>
        </form>
    </x-model-audit::panel>

    <x-model-audit::panel>
        @if ($entries->isEmpty())
            <x-model-audit::empty-state />
        @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-zinc-800 bg-zinc-900 text-xs font-semibold uppercase tracking-widest text-zinc-500">
                            <th class="px-4 py-3">{{ __('model-audit::ui.event') }}</th>
                            <th class="px-4 py-3">{{ __('model-audit::ui.subject') }}</th>
                            <th class="px-4 py-3">{{ __('model-audit::ui.actor') }}</th>
                            <th class="hidden px-4 py-3 sm:table-cell">{{ __('model-audit::ui.created_at') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800 text-xs text-zinc-400">
                        @foreach ($entries as $entry)
                            <tr class="transition hover:bg-zinc-900/70">
                                <td class="px-4 py-3">
                                    <a href="{{ route($routePrefix . 'show', $entry->uuid) }}" class="hover:text-white">
                                        <x-model-audit::event :event="$entry->event" />
                                    </a>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route($routePrefix . 'subject', ['type' => $entry->subject_type, 'id' => $entry->subject_id]) }}" class="block max-w-md truncate font-medium text-zinc-300 hover:text-white hover:underline">
                                        {{ $entry->subject_type }} [{{ $entry->subject_id }}]
                                    </a>
                                    <span class="mt-1 block font-mono text-xs text-zinc-600">{{ $entry->uuid }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($entry->actor_type !== null && $entry->actor_id !== null)
                                        <a href="{{ route($routePrefix . 'index', ['actor_type' => $entry->actor_type, 'actor_id' => $entry->actor_id]) }}" class="font-medium text-zinc-300 hover:text-white hover:underline">
                                            {{ $entry->actor_type }} [{{ $entry->actor_id }}]
                                        </a>
                                    @else
                                        <span class="text-zinc-500">{{ __('model-audit::ui.system') }}</span>
                                    @endif
                                </td>
                                <td class="hidden whitespace-nowrap px-4 py-3 sm:table-cell">
                                    <time datetime="{{ $entry->created_at?->toIso8601String() }}">{{ $entry->created_at?->translatedFormat('d M Y, H:i:s') }}</time>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-zinc-800 bg-zinc-900 px-4 py-3">
                {{ $entries->withQueryString()->links() }}
            </div>
        @endif
    </x-model-audit::panel>
</x-dynamic-component>
