<?php

namespace App\Http\Middleware;

use App\Traits\ResponseTrait;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->route('role'); 

        if(!in_array($role, ['user', 'admin', 'super_admin']))
        {
            return $this->returnError(__("roles.invalid_role"), 403);
        }

        $request->route()->forgetParameter('role');
        
        return $next($request);
    }
}
