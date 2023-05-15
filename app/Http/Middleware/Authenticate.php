<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\RefreshToken;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
        } catch (TokenInvalidException | TokenBlacklistedException $e) {
            return response()->json(['code' => 0, 'message' => 'Token is invalid or blacklisted'], 401);
        } catch (TokenExpiredException $e) {
            return  $this->handleExpiredToken($request, $next($request));
        } catch (UnauthorizedHttpException $e) {
            switch ($e->getMessage()) {
                case 'Token has expired and can no longer be refreshed':
                    return response()->json(['code' => 0, 'message' => 'Token is invalid or blacklisted'], 401);
                case 'Token has expired':
                    return $this->handleExpiredToken($request, $next($request));
                default:
                    return response()->json(['code' => 0, 'message' => 'Unauthorized'], 401);
            }
        } catch (Exception $e) {

            return response()->json(['code' => 0, 'message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }

    private function handleExpiredToken($request, $response)
    {
        try {
            $refreshToken = $this->auth->refresh();

            $this->auth->setToken($refreshToken)->toUser();

            $request->headers->set('Authorization', 'Bearer ' . $refreshToken);

            $data = json_decode($response->content(), true);
            $data['refresh_token'] = $refreshToken;

            return $response->setContent(json_encode($data));
        } catch (TokenExpiredException | TokenInvalidException | TokenBlacklistedException $e) {
            return response()->json(['code' => 0, 'message' => 'Token is invalid or blacklisted'], 401);
        }
    }
}
