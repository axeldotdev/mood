<?php

declare(strict_types=1);

use App\Models\User;

it('shows validation error when no mood is selected', function (): void {
    $user = User::factory()->withoutTwoFactor()->create();

    $page = visit(route('login'));

    $page->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('Log in')
        ->assertPathIs('/')
        ->assertSee('What is your mood today?');

    $page->click('Save your mood')
        ->assertSee('Please select at least one mood');

    expect($user->fresh()->moods)->toHaveCount(0);
});

it('displays the mood form on home page for authenticated users', function (): void {
    $user = User::factory()->withoutTwoFactor()->create();

    $page = visit(route('login'));

    $page->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('Log in')
        ->assertPathIs('/')
        ->assertSee('What is your mood today?')
        ->assertSee('Select one or two adjective and save it')
        ->assertSee('Good')
        ->assertSee('Great')
        ->assertSee('Peaceful')
        ->assertSee('Save your mood');
});
