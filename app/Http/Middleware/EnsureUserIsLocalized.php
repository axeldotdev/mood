<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use DateTimeZone;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsLocalized
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->setTimezone($request);
        $this->setLocale($request);

        return $next($request);
    }

    private function setTimezone(Request $request): void
    {
        $timezone = $request->cookie('timezone');

        if ($timezone && $this->isValidTimezone($timezone)) {
            date_default_timezone_set($timezone);
        }
    }

    private function isValidTimezone(string $timezone): bool
    {
        return in_array($timezone, DateTimeZone::listIdentifiers(), true);
    }

    private function setLocale(Request $request): void
    {
        $acceptLanguage = $request->header('Accept-Language');

        if (! $acceptLanguage) {
            return;
        }

        $locale = $this->parseAcceptLanguage($acceptLanguage);

        if ($locale) {
            app()->setLocale($locale);
            Carbon::setLocale($locale);
        }
    }

    private function parseAcceptLanguage(string $acceptLanguage): ?string
    {
        $parts = explode(',', $acceptLanguage);
        $firstPart = trim($parts[0]);

        $languageTag = explode(';', $firstPart)[0];

        $locale = explode('-', $languageTag)[0];

        if (preg_match('/^[a-z]{2}$/i', $locale)) {
            return strtolower($locale);
        }

        return null;
    }
}
