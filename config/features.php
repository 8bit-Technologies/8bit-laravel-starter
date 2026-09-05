<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Public Registration
    |--------------------------------------------------------------------------
    |
    | Disabled by default. Many applications built on this starter are
    | internal or invite-only, where every account is created by an
    | administrator through User Management rather than self-registration.
    |
    | Enable this to restore the /register route and its links throughout
    | the guest-facing UI. The Register Livewire component and view are
    | always present regardless of this setting.
    |
    */

    'registration_enabled' => false,

    /*
    |--------------------------------------------------------------------------
    | Email Verification
    |--------------------------------------------------------------------------
    |
    | Disabled by default. When enabled, the Member and Admin areas require
    | a verified email address in addition to authentication, restoring
    | Laravel's standard "verified" middleware behavior.
    |
    | The verification routes and UI remain reachable either way — this only
    | controls whether reaching them is *required* before using the app.
    |
    */

    'email_verification_enabled' => false,

];
