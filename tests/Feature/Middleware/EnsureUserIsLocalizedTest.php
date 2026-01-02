<?php

use Carbon\Carbon;

beforeEach(function (): void {
    date_default_timezone_set('UTC');
    app()->setLocale('en');
    Carbon::setLocale('en');
});

test('timezone is set from cookie', function (): void {
    $this->withCookie('timezone', 'Europe/Paris')
        ->get(route('home'));

    expect(date_default_timezone_get())->toBe('Europe/Paris');
});

test('invalid timezone falls back to UTC', function (): void {
    $this->withCookie('timezone', 'Invalid/Timezone')
        ->get(route('home'));

    expect(date_default_timezone_get())->toBe('UTC');
});

test('missing timezone cookie falls back to UTC', function (): void {
    $this->get(route('home'));

    expect(date_default_timezone_get())->toBe('UTC');
});

test('locale is set from Accept-Language header', function (): void {
    $this->withHeader('Accept-Language', 'fr')
        ->get(route('home'));

    expect(app()->getLocale())->toBe('fr')
        ->and(Carbon::getLocale())->toBe('fr');
});

test('complex Accept-Language extracts primary language', function (): void {
    $this->withHeader('Accept-Language', 'fr-FR,fr;q=0.9,en;q=0.8')
        ->get(route('home'));

    expect(app()->getLocale())->toBe('fr')
        ->and(Carbon::getLocale())->toBe('fr');
});

test('missing Accept-Language header falls back to default locale', function (): void {
    $this->get(route('home'));

    expect(app()->getLocale())->toBe('en');
});

test('both timezone and locale are set together', function (): void {
    $this->withCookie('timezone', 'America/New_York')
        ->withHeader('Accept-Language', 'es-ES,es;q=0.9')
        ->get(route('home'));

    expect(date_default_timezone_get())->toBe('America/New_York')
        ->and(app()->getLocale())->toBe('es')
        ->and(Carbon::getLocale())->toBe('es');
});
