<div>
    <div class="mb-6 text-center">
        <div class="text-xl font-extrabold">Verify your email</div>
        <div class="text-base-content/60 text-sm mt-1">
            Thanks for signing up! Before getting started, please verify your email address by clicking the link we just emailed to you.
        </div>
    </div>

    @if ($resent)
        <div class="alert alert-success mb-4">
            A new verification link has been sent to the email address you provided during registration.
        </div>
    @endif

    <div class="flex flex-col gap-3">
        <x-button label="Resend verification email" wire:click="sendVerification" class="btn-primary w-full" spinner="sendVerification" />

        <x-button label="Log out" wire:click="logout" class="btn-ghost w-full" spinner="logout" />
    </div>
</div>
