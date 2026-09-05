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

    <x-main>
        <x-slot:sidebar drawer="main-drawer" class="bg-base-100 lg:bg-inherit">
            <div class="px-5 pt-4 pb-2">
                <x-8bit.app-brand />
            </div>

            <x-menu activate-by-route>
                <x-menu-item title="Dashboard" icon="o-squares-2x2" route="dashboard" />
                <x-menu-item title="Profile" icon="o-user-circle" route="profile" />
            </x-menu>
        </x-slot:sidebar>

        <x-slot:content>
            <x-nav sticky class="rounded-box mb-5">
                <x-slot:brand>
                    <label for="main-drawer" class="lg:hidden">
                        <x-icon name="o-bars-3" class="cursor-pointer" />
                    </label>
                </x-slot:brand>

                <x-slot:actions>
                    <x-theme-toggle />

                    <x-dropdown right>
                        <x-slot:trigger>
                            <x-avatar
                                :title="auth()->user()->name"
                                placeholder="{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}"
                                class="cursor-pointer [&_.avatar]:!w-8 [&_.avatar]:!h-8"
                            />
                        </x-slot:trigger>

                        <x-menu-item title="Profile" icon="o-user-circle" route="profile" />

                        @can('access dashboard')
                            <x-menu-item title="Admin Panel" icon="o-shield-check" route="admin.dashboard" />
                        @endcan

                        <x-menu-separator />

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2 text-left">
                                    <x-icon name="o-arrow-right-start-on-rectangle" class="w-4 h-4" />
                                    Logout
                                </button>
                            </form>
                        </li>
                    </x-dropdown>
                </x-slot:actions>
            </x-nav>

            {{ $slot }}
        </x-slot:content>
    </x-main>

    <x-toast position="toast-top toast-end mt-20" />
</body>
</html>
