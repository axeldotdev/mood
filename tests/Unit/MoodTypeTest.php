<?php

declare(strict_types=1);

use App\Enums\MoodType;

it('returns pleasant category for pleasant moods', function (MoodType $mood): void {
    expect($mood->category())->toBe('pleasant');
})->with([
    'Accomplished' => MoodType::Accomplished,
    'Confident' => MoodType::Confident,
    'Content' => MoodType::Content,
    'Energetic' => MoodType::Energetic,
    'Enriching' => MoodType::Enriching,
    'Excited' => MoodType::Excited,
    'Focused' => MoodType::Focused,
    'Good' => MoodType::Good,
    'Grateful' => MoodType::Grateful,
    'Great' => MoodType::Great,
    'Hopeful' => MoodType::Hopeful,
    'Inspired' => MoodType::Inspired,
    'Joyful' => MoodType::Joyful,
    'Loved' => MoodType::Loved,
    'Peaceful' => MoodType::Peaceful,
    'Productive' => MoodType::Productive,
    'Stimulating' => MoodType::Stimulating,
]);

it('returns unpleasant category for unpleasant moods', function (MoodType $mood): void {
    expect($mood->category())->toBe('unpleasant');
})->with([
    'Angry' => MoodType::Angry,
    'Anxious' => MoodType::Anxious,
    'Bored' => MoodType::Bored,
    'Complicated' => MoodType::Complicated,
    'Confused' => MoodType::Confused,
    'Disappointing' => MoodType::Disappointing,
    'Exhausted' => MoodType::Exhausted,
    'Frustrating' => MoodType::Frustrating,
    'Irritated' => MoodType::Irritated,
    'Lonely' => MoodType::Lonely,
    'Overwhelmed' => MoodType::Overwhelmed,
    'Restless' => MoodType::Restless,
    'Sad' => MoodType::Sad,
    'Stressful' => MoodType::Stressful,
    'Worried' => MoodType::Worried,
]);
