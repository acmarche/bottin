<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RolesEnum;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class VerifyApiToken
{
    public const string AUTHENTICATED_ATTRIBUTE = 'api_authenticated';

    /**
     * Flag the request as authenticated when a valid bearer token is provided by
     * a user holding the API role. The token is optional: requests without one
     * (or with an invalid one) still pass through.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (is_string($token) && $token !== '') {
            $user = User::query()->where('api_token', $token)->first();

            if ($user instanceof User && $user->hasRole(RolesEnum::Api)) {
                $request->attributes->set(self::AUTHENTICATED_ATTRIBUTE, true);
            }
        }

        return $next($request);
    }
}
