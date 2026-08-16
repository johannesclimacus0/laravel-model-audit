@props(['value'])

<pre class="overflow-x-auto whitespace-pre-wrap break-words p-4 font-mono text-xs leading-6 text-zinc-400">
    {{ $value === null ? '—' : json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}
</pre>
