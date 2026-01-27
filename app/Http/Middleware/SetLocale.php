<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->session()->get('locale');

        if (!$locale) {
            $locale = $request->cookie('locale', config('app.locale'));
            if ($locale) {
                $request->session()->put('locale', $locale);
            }
        }

        App::setLocale($locale);

        return $next($request);
    }
}
