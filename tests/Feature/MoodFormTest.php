<?php

use App\Enums\MoodType;
use App\Models\Mood;
use App\Models\User;
use Livewire\Volt\Volt;

test('users can select up to two moods', function (): void {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('mood-form')
        ->assertSet('moods', [])
        ->set('moods', ['good'])
        ->assertSet('moods', ['good'])
        ->set('moods', ['good', 'great'])
        ->assertSet('moods', ['good', 'great']);
});

test('selecting more than two moods is trimmed to two', function (): void {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('mood-form')
        ->set('moods', ['good', 'great', 'peaceful'])
        ->assertSet('moods', ['good', 'great']);
});

test('mood checkboxes are rendered', function (): void {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('mood-form')
        ->assertSee(MoodType::Good->label())
        ->assertSee(MoodType::Great->label())
        ->assertSeeHtml('data-flux-checkbox-buttons');
});

test('user can deselect a mood when two are selected', function (): void {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('mood-form')
        ->set('moods', ['good', 'great'])
        ->assertSet('moods', ['good', 'great'])
        ->set('moods', ['good'])
        ->assertSet('moods', ['good']);
});

test('user can save a mood', function (): void {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('mood-form')
        ->set('moods', ['good', 'productive'])
        ->set('comment', 'Had a great day!')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('moods', [])
        ->assertSet('comment', '');

    expect($user->moods)->toHaveCount(1);
    expect($user->moods->first()->types->toArray())->toBe([MoodType::Good, MoodType::Productive]);
    expect($user->moods->first()->comment)->toBe('Had a great day!');
});

test('user can save a mood without a comment', function (): void {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('mood-form')
        ->set('moods', ['peaceful'])
        ->call('save')
        ->assertHasNoErrors();

    expect($user->moods)->toHaveCount(1);
    expect($user->moods->first()->comment)->toBeNull();
});

test('user cannot save mood without selecting at least one', function (): void {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('mood-form')
        ->set('moods', [])
        ->call('save')
        ->assertHasErrors(['moods']);
});

test('user sees already logged message when mood saved today', function (): void {
    $user = User::factory()->create();
    $mood = Mood::factory()->for($user)->create();

    Volt::actingAs($user)
        ->test('mood-form')
        ->assertSee(__("You've already logged your mood today!"))
        ->assertSee(__('Come back tomorrow to log how you feel.'))
        ->assertDontSee(__('What is your mood today?'));
});

test('user can save mood on different days', function (): void {
    $user = User::factory()->create();
    Mood::factory()->for($user)->create(['created_at' => now()->subDay()]);

    Volt::actingAs($user)
        ->test('mood-form')
        ->set('moods', ['good'])
        ->set('comment', 'Another good day!')
        ->call('save')
        ->assertHasNoErrors();

    expect($user->moods)->toHaveCount(2);
});
