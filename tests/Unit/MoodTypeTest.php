<?php

declare(strict_types=1);

use App\Enums\MoodType;

it('returns pleasant category for pleasant moods', function (MoodType $mood): void {
    expect($mood->category())->toBe('pleasant');
})->with([
    'Enriching' => MoodType::Enriching,
    'Good' => MoodType::Good,
    'Great' => MoodType::Great,
    'Joyful' => MoodType::Joyful,
    'Peaceful' => MoodType::Peaceful,
    'Productive' => MoodType::Productive,
    'Stimulating' => MoodType::Stimulating,
]);

it('returns unpleasant category for unpleasant moods', function (MoodType $mood): void {
    expect($mood->category())->toBe('unpleasant');
})->with([
    'Complicated' => MoodType::Complicated,
    'Disappointing' => MoodType::Disappointing,
    'Frustrating' => MoodType::Frustrating,
    'Sad' => MoodType::Sad,
    'Stressful' => MoodType::Stressful,
]);
