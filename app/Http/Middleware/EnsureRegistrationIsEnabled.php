<?php

namespace App\Http\Middleware;

use App\Support\AuthenticationFeatures;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegistrationIsEnabled
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(AuthenticationFeatures::registrationEnabled(), 404);

        return $next($request);
    }
}
