@props(['event'])

@php
    $translationKey = 'model-audit::events.' . $event;
    $translatedEvent = __($translationKey);
    $eventLabel = $translatedEvent === $translationKey ? $event : $translatedEvent;
@endphp

<span @class([
    'inline-block whitespace-nowrap border-l-2 border-zinc-600 py-0.5 pl-2 font-mono text-xs text-zinc-300',
    'border-emerald-700' => in_array($event, ['created', 'restored'], true),
    'border-blue-700' => $event === 'updated',
    'border-red-700' => $event === 'deleted',
])>{{ $eventLabel }}</span>
