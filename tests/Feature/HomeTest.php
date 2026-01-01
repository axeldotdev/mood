<?php

declare(strict_types=1);

use App\Enums\MoodType;
use App\Models\User;
use Livewire\Volt\Volt;

test('home page is accessible', function (): void {
    $response = $this->get(route('home'));

    $response->assertStatus(200);
});

test('guest can fill mood form and data is stored in session', function (): void {
    Volt::test('mood-form')
        ->set('moods', ['good', 'productive'])
        ->set('comment', 'Today was great!')
        ->call('save')
        ->assertHasNoErrors();

    expect(session('pending_mood'))->toBe([
        'types' => ['good', 'productive'],
        'comment' => 'Today was great!',
    ]);
});

test('guest mood validation works before storing in session', function (): void {
    Volt::test('mood-form')
        ->set('moods', [])
        ->call('save')
        ->assertHasErrors(['moods']);

    expect(session('pending_mood'))->toBeNull();
});

test('pending mood is saved after user logs in', function (): void {
    $user = User::factory()->withoutTwoFactor()->create();

    session(['pending_mood' => [
        'types' => ['good', 'productive'],
        'comment' => 'Today was great!',
    ]]);

    Volt::test('auth.login-modal')
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    expect(session('pending_mood'))->toBeNull();
    expect($user->fresh()->moods)->toHaveCount(1);
    expect($user->fresh()->moods->first()->types->toArray())->toBe([MoodType::Good, MoodType::Productive]);
    expect($user->fresh()->moods->first()->comment)->toBe('Today was great!');
});

test('pending mood is saved after user registers', function (): void {
    session(['pending_mood' => [
        'types' => ['peaceful'],
        'comment' => 'Feeling calm today.',
    ]]);

    Volt::test('auth.register-modal')
        ->set('name', 'New User')
        ->set('email', 'newuser@example.com')
        ->set('password', 'Xk9$mP2vL@nQ7wR!')
        ->set('password_confirmation', 'Xk9$mP2vL@nQ7wR!')
        ->call('register')
        ->assertRedirect(route('dashboard'));

    $user = User::where('email', 'newuser@example.com')->first();
    expect(session('pending_mood'))->toBeNull();
    expect($user->moods)->toHaveCount(1);
    expect($user->moods->first()->types->toArray())->toBe([MoodType::Peaceful]);
    expect($user->moods->first()->comment)->toBe('Feeling calm today.');
});

test('no mood is saved if there is no pending mood in session', function (): void {
    $user = User::factory()->withoutTwoFactor()->create();

    Volt::test('auth.login-modal')
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    expect($user->fresh()->moods)->toHaveCount(0);
});

test('pending mood is not saved if user already has a mood today', function (): void {
    $user = User::factory()->withoutTwoFactor()->create();
    $user->moods()->create([
        'types' => ['peaceful'],
        'comment' => 'Morning mood',
    ]);

    session(['pending_mood' => [
        'types' => ['good'],
        'comment' => 'Evening mood!',
    ]]);

    Volt::test('auth.login-modal')
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    expect(session('pending_mood'))->toBeNull();
    expect($user->fresh()->moods)->toHaveCount(1);
});

test('login fails with invalid credentials', function (): void {
    $user = User::factory()->withoutTwoFactor()->create();

    Volt::test('auth.login-modal')
        ->set('email', $user->email)
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['email']);
});

test('register fails with invalid email', function (): void {
    Volt::test('auth.register-modal')
        ->set('name', 'Test User')
        ->set('email', 'not-an-email')
        ->set('password', 'Xk9$mP2vL@nQ7wR!')
        ->set('password_confirmation', 'Xk9$mP2vL@nQ7wR!')
        ->call('register')
        ->assertHasErrors(['email']);
});

test('register fails when passwords do not match', function (): void {
    Volt::test('auth.register-modal')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'Xk9$mP2vL@nQ7wR!')
        ->set('password_confirmation', 'different-password')
        ->call('register')
        ->assertHasErrors(['password']);
});
