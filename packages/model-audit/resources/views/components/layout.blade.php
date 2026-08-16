@props([
    'title' => __('model-audit::ui.title'),
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    @unless (app()->runningUnitTests())
        @vite(config('model-audit.ui.vite_assets', ['resources/css/app.css']))
    @endunless
</head>
<body class="min-h-screen bg-zinc-950 font-sans text-zinc-200 antialiased">
    <main class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>
</body>
</html>
