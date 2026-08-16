@props(['result'])

<div @class([
    'flex flex-wrap items-center gap-2 border px-4 py-3 text-xs',
    'border-emerald-900 bg-emerald-950/40 text-emerald-300' => $result->valid,
    'border-red-900 bg-red-950/40 text-red-300' => !$result->valid,
])>
    <strong>{{ $result->valid ? __('model-audit::ui.integrity_valid') : __('model-audit::ui.integrity_invalid') }}</strong>

    @if (!$result->valid && $result->failure)
        <span>{{ __('model-audit::ui.failures.' . $result->failure->value) }}</span>
    @endif

    @if (!$result->valid && $result->failedEntryUuid)
        <span class="font-mono">{{ $result->failedEntryUuid }}</span>
    @endif
</div>
