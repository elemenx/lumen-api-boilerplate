<?php

namespace App\Essential\Middleware;

use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Auth\Access\AuthorizationException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class RefreshToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $this->checkForToken($request); // Check presence of a token.

        try {
            if (!$this->auth->parseToken()->authenticate()) { // Check user not found. Check token has expired.
                throw new AuthorizationException('User not found');
            }
            $payload = $this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray();
            $user = $this->auth->user();

            if (!empty($user->reset_at) && $payload['iat'] < Carbon::parse($user->reset_at)->timestamp) {
                throw new TokenInvalidException();
            }
            return $next($request); // Token is valid. User logged. Response without any token.
        } catch (TokenExpiredException $t) { // Token expired. User not logged.
            $payload = $this->auth->manager()->getPayloadFactory()->buildClaimsCollection()->toPlainArray();
            $key = 'block_refresh_token_for_user_' . $payload['sub'];
            $cachedBefore = (int)Cache::has($key);
            if ($cachedBefore) { // If a token alredy was refreshed and sent to the client in the last JWT_BLACKLIST_GRACE_PERIOD seconds.
                Auth::onceUsingId($payload['sub']); // Log the user using id.
                return $next($request); // Token expired. Response without any token because in grace period.
            }
            try {
                $newtoken = $this->auth->refresh(); // Get new token.
                $gracePeriod = $this->auth->manager()->getBlacklist()->getGracePeriod();
                $expiresAt = Carbon::now()->addSeconds($gracePeriod);
                Cache::put($key, $newtoken, $expiresAt);
            } catch (JWTException $e) {
                throw new AuthorizationException($e->getMessage());
            }
        }

        $response = $next($request); // Token refreshed and continue.

        return $this->setAuthenticationHeader($response, $newtoken); // Response with new token on header Authorization.
    }
}
