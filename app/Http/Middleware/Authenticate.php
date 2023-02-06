<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\RefreshToken;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;

class Authenticate extends RefreshToken
{
    // /**
    //  * The authentication guard factory instance.
    //  *
    //  * @var \Illuminate\Contracts\Auth\Factory
    //  */
    // protected $auth;

    // /**
    //  * Create a new middleware instance.
    //  *
    //  * @param  \Illuminate\Contracts\Auth\Factory  $auth
    //  * @return void
    //  */
    // public function __construct(Auth $auth)
    // {
    //     $this->auth = $auth;
    // }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        try {
            $user = $this->authenticate($request);
        } catch (Exception $e) {
            if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['code' => 0, 'message' => 'Token is invalid'], 401);
            } else if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException) {
                $this->handleExpiredToken($request, $next($request));
            } else if ($e instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException && $e->getMessage() == 'Token has expired') {
                if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException) {
                    return $this->handleExpiredToken($request, $next($request));
                }
                return $this->handleExpiredToken($request, $next($request));
            }
        }

        return $next($request);
    }
    private function handleExpiredToken($request, $response)
    {
        $refreshToken = $this->auth->refresh();

        $this->auth->setToken($refreshToken)->toUser();

        $request->headers->set('Authorization', 'Bearer ' . $refreshToken);

        $data = json_decode($response->content(), true);
        $data['refresh_token'] = $refreshToken;


        return $response->setContent(json_encode($data));
    }
}
