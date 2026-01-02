<?php

use App\Models\Goal;
use App\Models\User;
use Livewire\Volt\Volt;

test('user can save a goal', function (): void {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('goal-form')
        ->set('name', 'Learn Laravel')
        ->set('description', 'Master the Laravel framework')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('name', '')
        ->assertSet('description', null);

    $user->refresh();

    expect($user->goals)->toHaveCount(1);
    expect($user->goals->first()->name)->toBe('Learn Laravel');
    expect($user->goals->first()->description)->toBe('Master the Laravel framework');
});

test('user can save a goal without a description', function (): void {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('goal-form')
        ->set('name', 'Learn Laravel')
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();

    expect($user->goals)->toHaveCount(1);
    expect($user->goals->first()->name)->toBe('Learn Laravel');
    expect($user->goals->first()->description)->toBeNull();
});

test('user cannot save a goal without a name', function (): void {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('goal-form')
        ->set('name', '')
        ->set('description', 'Some description')
        ->call('save')
        ->assertHasErrors(['name']);

    $user->refresh();

    expect($user->goals)->toHaveCount(0);
});

test('goal name cannot exceed 255 characters', function (): void {
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('goal-form')
        ->set('name', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['name']);

    $user->refresh();

    expect($user->goals)->toHaveCount(0);
});

test('saved goals appear in the list', function (): void {
    $user = User::factory()->create();
    $goal = Goal::factory()->for($user)->create(['name' => 'Existing Goal']);

    Volt::actingAs($user)
        ->test('goal-form')
        ->assertSee('Existing Goal');
});

test('user can update goal progress', function (): void {
    $user = User::factory()->create();
    $goal = Goal::factory()->for($user)->create(['progress' => 0]);

    Volt::actingAs($user)
        ->test('goal-form')
        ->call('updateProgress', $goal, 50);

    $goal->refresh();

    expect($goal->progress)->toBe(50);
});

test('updating progress sets start_at if null', function (): void {
    $user = User::factory()->create();
    $goal = Goal::factory()->for($user)->create(['start_at' => null]);

    expect($goal->start_at)->toBeNull();

    Volt::actingAs($user)
        ->test('goal-form')
        ->call('updateProgress', $goal, 10);

    $goal->refresh();

    expect($goal->start_at)->not->toBeNull();
});

test('updating progress does not overwrite existing start_at', function (): void {
    $user = User::factory()->create();
    $originalStartAt = now()->subDays(5);
    $goal = Goal::factory()->for($user)->create(['start_at' => $originalStartAt]);

    Volt::actingAs($user)
        ->test('goal-form')
        ->call('updateProgress', $goal, 20);

    $goal->refresh();

    expect($goal->start_at->toDateTimeString())->toBe($originalStartAt->toDateTimeString());
});

test('goal progress is displayed in the slider', function (): void {
    $user = User::factory()->create();
    $goal = Goal::factory()->for($user)->create(['name' => 'Test Goal', 'progress' => 30]);

    Volt::actingAs($user)
        ->test('goal-form')
        ->assertSee('30%');
});

test('user can complete a goal', function (): void {
    $user = User::factory()->create();
    $goal = Goal::factory()->for($user)->create();

    expect($goal->completed_at)->toBeNull();

    Volt::actingAs($user)
        ->test('goal-form')
        ->call('complete', $goal);

    $goal->refresh();

    expect($goal->completed_at)->not->toBeNull();
});

test('user can cancel a goal', function (): void {
    $user = User::factory()->create();
    $goal = Goal::factory()->for($user)->create();

    expect($goal->canceled_at)->toBeNull();

    Volt::actingAs($user)
        ->test('goal-form')
        ->call('cancel', $goal);

    $goal->refresh();

    expect($goal->canceled_at)->not->toBeNull();
});

test('updating progress to 100 sets completed_at', function (): void {
    $user = User::factory()->create();
    $goal = Goal::factory()->for($user)->create(['progress' => 50]);

    expect($goal->completed_at)->toBeNull();

    Volt::actingAs($user)
        ->test('goal-form')
        ->call('updateProgress', $goal, 100);

    $goal->refresh();

    expect($goal->progress)->toBe(100);
    expect($goal->completed_at)->not->toBeNull();
});

test('updating progress below 100 clears completed_at', function (): void {
    $user = User::factory()->create();
    $goal = Goal::factory()->for($user)->create([
        'progress' => 100,
        'completed_at' => now(),
    ]);

    expect($goal->completed_at)->not->toBeNull();

    Volt::actingAs($user)
        ->test('goal-form')
        ->call('updateProgress', $goal, 80);

    $goal->refresh();

    expect($goal->progress)->toBe(80);
    expect($goal->completed_at)->toBeNull();
});
