<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const SUPPORTED = ['es', 'en'];

    public function handle(Request $request, Closure $next, ?string $locale = null): Response
    {
        $locale = in_array($locale, self::SUPPORTED, true) ? $locale : config('app.locale');

        app()->setLocale($locale);
        $request->attributes->set('locale', $locale);

        return $next($request);
    }
}
