<?php

namespace App\Support;

final class AuthenticationFeatures
{
    public static function registrationEnabled(): bool
    {
        return (bool) config('features.registration_enabled');
    }

    public static function emailVerificationEnabled(): bool
    {
        return (bool) config('features.email_verification_enabled');
    }
}
