<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="description" content="8bit Laravel Starter — a production-ready Laravel application foundation with authentication, member and admin panels, roles & permissions, and user management already structured for extension.">
    <title>{{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-base-100">

    <x-nav sticky>
        <x-slot:brand>
            <x-8bit.app-brand />
        </x-slot:brand>

        <x-slot:actions>
            <x-theme-toggle />

            @auth
                <x-button label="Dashboard" link="{{ route('dashboard') }}" class="btn-primary" />
            @else
                <x-button label="Log in" link="{{ route('login') }}" class="btn-ghost" />
                <x-button label="Register" link="{{ route('register') }}" class="btn-primary" />
            @endauth
        </x-slot:actions>
    </x-nav>

    <main>
        {{-- HERO --}}
        <section class="max-w-5xl mx-auto px-6 py-20 text-center">
            <div class="inline-flex items-center gap-2 rounded-full border border-base-content/10 px-4 py-1 text-sm text-base-content/70 mb-6">
                <x-icon name="o-cube" class="w-4 h-4" />
                8bit Laravel Starter
            </div>

            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight mb-6">
                A reusable Laravel application foundation
            </h1>

            <p class="text-lg text-base-content/70 max-w-2xl mx-auto mb-8">
                A production-ready Laravel application foundation built with Laravel, Livewire, Mary UI, Tailwind CSS,
                and daisyUI — with authentication, member and admin panels, roles &amp; permissions, and user
                management already structured for extension.
            </p>

            <div class="flex items-center justify-center gap-3">
                <x-button label="Get Started" icon="o-arrow-right" link="https://github.com/8bit-Technologies/8bit-laravel-starter" class="btn-primary" external />
                <x-button label="Log in" link="{{ route('login') }}" class="btn-ghost" />
            </div>
        </section>

        {{-- WHAT IT PROVIDES --}}
        <section class="max-w-5xl mx-auto px-6 py-12">
            <h2 class="text-2xl font-bold text-center mb-10">What it provides</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <x-card shadow>
                    <div class="flex items-start gap-3">
                        <x-icon name="o-shield-check" class="w-6 h-6 text-primary shrink-0" />
                        <div>
                            <div class="font-semibold mb-1">Authentication</div>
                            <p class="text-sm text-base-content/70">Login, registration, logout, password reset, and email verification.</p>
                        </div>
                    </div>
                </x-card>

                <x-card shadow>
                    <div class="flex items-start gap-3">
                        <x-icon name="o-squares-2x2" class="w-6 h-6 text-primary shrink-0" />
                        <div>
                            <div class="font-semibold mb-1">Member Panel</div>
                            <p class="text-sm text-base-content/70">A clean authenticated area with a dashboard and profile management.</p>
                        </div>
                    </div>
                </x-card>

                <x-card shadow>
                    <div class="flex items-start gap-3">
                        <x-icon name="o-rectangle-group" class="w-6 h-6 text-primary shrink-0" />
                        <div>
                            <div class="font-semibold mb-1">Admin Panel</div>
                            <p class="text-sm text-base-content/70">A grouped, permission-aware sidebar for managing the application.</p>
                        </div>
                    </div>
                </x-card>

                <x-card shadow>
                    <div class="flex items-start gap-3">
                        <x-icon name="o-key" class="w-6 h-6 text-primary shrink-0" />
                        <div>
                            <div class="font-semibold mb-1">Roles &amp; Permissions</div>
                            <p class="text-sm text-base-content/70">Spatie Permission with a protected Super Admin and a dynamic Roles &amp; Permissions manager.</p>
                        </div>
                    </div>
                </x-card>

                <x-card shadow>
                    <div class="flex items-start gap-3">
                        <x-icon name="o-users" class="w-6 h-6 text-primary shrink-0" />
                        <div>
                            <div class="font-semibold mb-1">User Management</div>
                            <p class="text-sm text-base-content/70">Create, edit, and manage users with role assignment built in.</p>
                        </div>
                    </div>
                </x-card>

                <x-card shadow>
                    <div class="flex items-start gap-3">
                        <x-icon name="o-swatch" class="w-6 h-6 text-primary shrink-0" />
                        <div>
                            <div class="font-semibold mb-1">Consistent UI</div>
                            <p class="text-sm text-base-content/70">Responsive layouts and dark mode, built on Mary UI, Tailwind CSS, and daisyUI.</p>
                        </div>
                    </div>
                </x-card>
            </div>
        </section>

        {{-- TECH STACK --}}
        <section class="max-w-5xl mx-auto px-6 py-12">
            <h2 class="text-2xl font-bold text-center mb-8">Technology stack</h2>

            <div class="flex flex-wrap items-center justify-center gap-2">
                <x-badge value="Laravel 13" class="badge-lg badge-outline" />
                <x-badge value="Livewire 4" class="badge-lg badge-outline" />
                <x-badge value="Mary UI" class="badge-lg badge-outline" />
                <x-badge value="Tailwind CSS 4" class="badge-lg badge-outline" />
                <x-badge value="daisyUI 5" class="badge-lg badge-outline" />
                <x-badge value="Spatie Permission" class="badge-lg badge-outline" />
                <x-badge value="Pest" class="badge-lg badge-outline" />
            </div>
        </section>
    </main>

    <footer class="border-t border-base-content/10 mt-12">
        <div class="max-w-5xl mx-auto px-6 py-8 text-center text-sm text-base-content/60">
            8bit Laravel Starter &middot; Built on Laravel {{ app()->version() }}
        </div>
    </footer>

</body>
</html>
