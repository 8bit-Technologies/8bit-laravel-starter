<div>
    <div class="mb-6 text-center">
        <div class="text-xl font-extrabold">Sign in</div>
        <div class="text-base-content/60 text-sm mt-1">Welcome back. Please enter your details.</div>
    </div>

    @if (session('status'))
        <div class="alert alert-success mb-4">{{ session('status') }}</div>
    @endif

    <x-form wire:submit="login">
        <x-input label="Email" wire:model="email" type="email" autofocus autocomplete="username" required />

        <x-password label="Password" wire:model="password" autocomplete="current-password" required />

        <div class="flex items-center justify-between -mt-1">
            <x-checkbox label="Remember me" wire:model="remember" />

            <a href="{{ route('password.request') }}" wire:navigate class="text-sm link link-hover">
                Forgot your password?
            </a>
        </div>

        <x-slot:actions>
            <x-button label="Log in" type="submit" class="btn-primary w-full" spinner="login" />
        </x-slot:actions>
    </x-form>

    <div class="text-center text-sm mt-6">
        Don't have an account?
        <a href="{{ route('register') }}" wire:navigate class="link link-hover font-semibold">Sign up</a>
    </div>
</div>
