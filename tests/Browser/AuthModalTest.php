<?php

declare(strict_types=1);

use App\Models\User;

it('opens login modal when clicking sign in button', function (): void {
    $page = visit(route('home'));

    $page->assertSee('Sign in')
        ->click('Sign in')
        ->assertSee('Log in to your account')
        ->assertSee('Enter your email and password below to log in');
});

it('opens register modal when clicking sign up button', function (): void {
    $page = visit(route('home'));

    $page->assertSee('Sign up')
        ->click('Sign up')
        ->assertSee('Create an account')
        ->assertSee('Enter your details below to create your account');
});

it('can switch from login modal to register modal', function (): void {
    $page = visit(route('home'));

    $page->click('Sign in')
        ->assertSee('Log in to your account')
        ->click('@switch-to-register')
        ->waitForText('Create an account')
        ->assertSee('Create an account');
});

it('can switch from register modal to login modal', function (): void {
    $page = visit(route('home'));

    $page->click('Sign up')
        ->assertSee('Create an account')
        ->click('@switch-to-login')
        ->waitForText('Log in to your account')
        ->assertSee('Log in to your account');
});

it('can login via modal', function (): void {
    $user = User::factory()->withoutTwoFactor()->create();

    $page = visit(route('home'));

    $page->click('Sign in')
        ->assertSee('Log in to your account')
        ->fill('email', $user->email)
        ->fill('password', 'password')
        ->click('Log in')
        ->assertPathIs('/')
        ->assertSee('What is your mood today?');
});

it('can register via modal', function (): void {
    $page = visit(route('home'));

    $page->click('Sign up')
        ->assertSee('Create an account')
        ->fill('@register-name', 'Test User')
        ->fill('@register-email', 'browsertest@example.com')
        ->fill('@register-password', 'Xk9$mP2vL@nQ7wR!')
        ->fill('@register-password-confirmation', 'Xk9$mP2vL@nQ7wR!')
        ->click('@register-user-button')
        ->waitForText('What is your mood today?')
        ->assertPathIs('/');

    expect(User::where('email', 'browsertest@example.com')->exists())->toBeTrue();
});

it('can switch from login modal to forgot password modal', function (): void {
    $page = visit(route('home'));

    $page->click('Sign in')
        ->assertSee('Log in to your account')
        ->click('@switch-to-forgot-password')
        ->waitForText('Forgot password')
        ->assertSee('Enter your email to receive a password reset link');
});

it('can switch from forgot password modal to login modal', function (): void {
    $page = visit(route('home'));

    $page->click('Sign in')
        ->assertSee('Log in to your account')
        ->click('@switch-to-forgot-password')
        ->waitForText('Forgot password')
        ->click('@forgot-password-switch-to-login')
        ->waitForText('Log in to your account')
        ->assertSee('Log in to your account');
});
