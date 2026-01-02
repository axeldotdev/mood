<?php

declare(strict_types=1);

use App\Enums\GoalStatus;
use App\Models\Goal;

it('returns Unstarted when no dates are set', function (): void {
    $goal = Goal::factory()->make([
        'start_at' => null,
        'completed_at' => null,
        'canceled_at' => null,
    ]);

    expect($goal->status)->toBe(GoalStatus::Unstarted);
});

it('returns Started when start_at is set', function (): void {
    $goal = Goal::factory()->make([
        'start_at' => now(),
        'completed_at' => null,
        'canceled_at' => null,
    ]);

    expect($goal->status)->toBe(GoalStatus::Started);
});

it('returns Completed when completed_at is set', function (): void {
    $goal = Goal::factory()->make([
        'start_at' => now()->subDay(),
        'completed_at' => now(),
        'canceled_at' => null,
    ]);

    expect($goal->status)->toBe(GoalStatus::Completed);
});

it('returns Canceled when canceled_at is set', function (): void {
    $goal = Goal::factory()->make([
        'start_at' => now()->subDay(),
        'completed_at' => null,
        'canceled_at' => now(),
    ]);

    expect($goal->status)->toBe(GoalStatus::Canceled);
});

it('prioritizes Canceled over Completed', function (): void {
    $goal = Goal::factory()->make([
        'start_at' => now()->subDays(2),
        'completed_at' => now()->subDay(),
        'canceled_at' => now(),
    ]);

    expect($goal->status)->toBe(GoalStatus::Canceled);
});

it('has correct color for each status', function (GoalStatus $status, string $expectedColor): void {
    expect($status->color())->toBe($expectedColor);
})->with([
    'unstarted' => [GoalStatus::Unstarted, 'zinc'],
    'started' => [GoalStatus::Started, 'sky'],
    'completed' => [GoalStatus::Completed, 'emerald'],
    'canceled' => [GoalStatus::Canceled, 'red'],
]);

it('has correct label for each status', function (GoalStatus $status, string $expectedLabel): void {
    expect($status->label())->toBe($expectedLabel);
})->with([
    'unstarted' => [GoalStatus::Unstarted, 'Unstarted'],
    'started' => [GoalStatus::Started, 'Started'],
    'completed' => [GoalStatus::Completed, 'Completed'],
    'canceled' => [GoalStatus::Canceled, 'Canceled'],
]);
