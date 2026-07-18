<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class Locale
{
    public const SUPPORTED = ['es', 'en'];

    public const DEFAULT = 'es';

    /**
     * Build the equivalent URL for the given locale, keeping the current route.
     */
    public static function switchUrl(string $locale): string
    {
        $current = Route::currentRouteName();

        if (! $current) {
            return $locale === self::DEFAULT ? url('/') : url('/'.$locale);
        }

        // Route names are prefixed with "en." for the English variant.
        $base = preg_replace('/^en\./', '', $current);
        $name = $locale === self::DEFAULT ? $base : 'en.'.$base;

        if (! Route::has($name)) {
            return $locale === self::DEFAULT ? url('/') : url('/'.$locale);
        }

        $params = Route::current()?->parameters() ?? [];

        return route($name, $params, true);
    }

    /**
     * Generate a URL for a public route in the current locale.
     * English routes are registered with the "en." name prefix.
     */
    public static function route(string $name, mixed $params = []): string
    {
        $prefixed = app()->getLocale() === 'en' ? 'en.'.$name : $name;

        if (! Route::has($prefixed)) {
            $prefixed = $name;
        }

        return route($prefixed, $params);
    }

    public static function current(): string
    {
        return app()->getLocale();
    }

    public static function other(): string
    {
        return app()->getLocale() === 'es' ? 'en' : 'es';
    }
}
