<?php

namespace App\Http\Middleware;

use App\Support\AuthenticationFeatures;
use Closure;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Wraps Laravel's native "verified" middleware so the requirement can be
 * toggled centrally through config/features.php, without scattering
 * conditionals through routes or Livewire components. When email
 * verification is disabled, this is a no-op pass-through.
 */
class EnsureEmailIsVerifiedIfRequired
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ?string $redirectToRoute = null): Response
    {
        if (! AuthenticationFeatures::emailVerificationEnabled()) {
            return $next($request);
        }

        return (new EnsureEmailIsVerified)->handle($request, $next, $redirectToRoute);
    }
}
