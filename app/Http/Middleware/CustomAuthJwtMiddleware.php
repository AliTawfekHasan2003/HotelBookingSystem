<?php

namespace App\Http\Middleware;

use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class CustomAuthJwtMiddleWare
{
    use ResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return $this->returnError(__('auth.errors.token_expired'), 401);
        } catch (TokenInvalidException $e) {
            return $this->returnError(__('auth.errors.token_invalid'), 401);
        } catch (JWTException $e) {
            return $this->returnError(__('auth.errors.token_not_provided'), 401);
        } catch (\Throwable $e) {
            return $this->returnError(__('errors.unexpected_error'), 500);
        }
        $request->attributes->set('user', $user);

        return $next($request);
    }
}
