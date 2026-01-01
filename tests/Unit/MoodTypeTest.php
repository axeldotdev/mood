<?php

declare(strict_types=1);

use App\Enums\MoodType;

it('returns positive category for positive moods', function (MoodType $mood): void {
    expect($mood->category())->toBe('positive');
})->with([
    'Enriching' => MoodType::Enriching,
    'Good' => MoodType::Good,
    'Great' => MoodType::Great,
    'Joyful' => MoodType::Joyful,
    'Peaceful' => MoodType::Peaceful,
    'Productive' => MoodType::Productive,
    'Stimulating' => MoodType::Stimulating,
]);

it('returns negative category for negative moods', function (MoodType $mood): void {
    expect($mood->category())->toBe('negative');
})->with([
    'Complicated' => MoodType::Complicated,
    'Disappointing' => MoodType::Disappointing,
    'Frustrating' => MoodType::Frustrating,
    'Sad' => MoodType::Sad,
    'Stressful' => MoodType::Stressful,
]);
