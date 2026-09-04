<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-base-200">
    <div class="flex min-h-screen flex-col items-center justify-center gap-6 p-6">
        <x-8bit.app-brand />

        <x-card class="w-full max-w-sm shadow-xs" shadow>
            {{ $slot }}
        </x-card>
    </div>

    <x-toast />
</body>
</html>
