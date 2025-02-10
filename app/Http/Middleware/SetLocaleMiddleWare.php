<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {  
        $supportedLocales = ['en', 'ar'];
        $locale = $request->header('Accept-Language', 'en');

        if (!in_array($locale, $supportedLocales)) {
            $locale = 'en';
        }

        App::setlocale($locale);

        return $next($request);
    }
}
