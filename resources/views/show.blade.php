@php
    $routePrefix = rtrim((string) config('model-audit.ui.route_name_prefix', 'model-audit.'), '.') . '.';
@endphp

<x-dynamic-component
    :component="config('model-audit.ui.layout', 'model-audit::layout')"
    :title="__('model-audit::ui.entry_title', ['uuid' => $entry->uuid])"
>
    <x-model-audit::page-header
        :title="$entry->uuid"
        :back-url="route($routePrefix . 'index')"
        mono
    >
        <x-slot:meta><x-model-audit::event :event="$entry->event" /></x-slot:meta>
    </x-model-audit::page-header>

    <div class="grid gap-4">
        <x-model-audit::panel :title="__('model-audit::ui.entry_details')">
            <dl>
                <x-model-audit::property label="UUID" class="font-mono">{{ $entry->uuid }}</x-model-audit::property>
                <x-model-audit::property :label="__('model-audit::ui.event')"><x-model-audit::event :event="$entry->event" /></x-model-audit::property>
                <x-model-audit::property :label="__('model-audit::ui.subject')" class="font-mono">
                    <a href="{{ route($routePrefix . 'subject', ['type' => $entry->subject_type, 'id' => $entry->subject_id]) }}" class="hover:text-white hover:underline">{{ $entry->subject_type }} [{{ $entry->subject_id }}]</a>
                </x-model-audit::property>
                <x-model-audit::property :label="__('model-audit::ui.actor')" class="font-mono">{{ $entry->actor_type !== null && $entry->actor_id !== null ? $entry->actor_type . ' [' . $entry->actor_id . ']' : __('model-audit::ui.system') }}</x-model-audit::property>
                <x-model-audit::property :label="__('model-audit::ui.created_at')"><time datetime="{{ $entry->created_at?->toIso8601String() }}">{{ $entry->created_at?->translatedFormat('d M Y, H:i:s T') }}</time></x-model-audit::property>
            </dl>
        </x-model-audit::panel>

        <x-model-audit::panel :title="__('model-audit::ui.changes')">
            <div class="grid divide-y divide-zinc-800 lg:grid-cols-2 lg:divide-x lg:divide-y-0">
                <div>
                    <div class="border-b border-zinc-800 px-4 py-2 text-xs uppercase tracking-widest text-zinc-500">{{ __('model-audit::ui.old_values') }}</div>
                    <x-model-audit::json :value="$entry->old_values" />
                </div>
                <div>
                    <div class="border-b border-zinc-800 px-4 py-2 text-xs uppercase tracking-widest text-zinc-500">{{ __('model-audit::ui.new_values') }}</div>
                    <x-model-audit::json :value="$entry->new_values" />
                </div>
            </div>
        </x-model-audit::panel>

        <div class="grid gap-4 lg:grid-cols-2">
            <x-model-audit::panel :title="__('model-audit::ui.metadata')">
                <x-model-audit::json :value="$entry->metadata" />
            </x-model-audit::panel>
            <x-model-audit::panel :title="__('model-audit::ui.request_context')">
                <dl>
                    <x-model-audit::property :label="__('model-audit::ui.ip_address')" class="font-mono">{{ $entry->ip_address ?? '—' }}</x-model-audit::property>
                    <x-model-audit::property label="User-Agent" class="font-mono">{{ $entry->user_agent ?? '—' }}</x-model-audit::property>
                    <x-model-audit::property :label="__('model-audit::ui.request_id')" class="font-mono">{{ $entry->request_id ?? '—' }}</x-model-audit::property>
                </dl>
            </x-model-audit::panel>
        </div>

        <x-model-audit::panel :title="__('model-audit::ui.integrity_data')">
            <dl>
                <x-model-audit::property :label="__('model-audit::ui.previous_hash')" class="font-mono">{{ $entry->previous_hash ?? '—' }}</x-model-audit::property>
                <x-model-audit::property :label="__('model-audit::ui.hash')" class="font-mono">{{ $entry->hash ?? '—' }}</x-model-audit::property>
            </dl>
        </x-model-audit::panel>
    </div>
</x-dynamic-component>
