<?php

declare(strict_types=1);

namespace App\Enums;

enum MoodType: string
{
    case Complicated = 'complicated';
    case Disappointing = 'disappointing';
    case Enriching = 'enriching';
    case Frustrating = 'frustrating';
    case Good = 'good';
    case Great = 'great';
    case Joyful = 'joyful';
    case Peaceful = 'peaceful';
    case Productive = 'productive';
    case Sad = 'sad';
    case Stimulating = 'stimulating';
    case Stressful = 'stressful';

    public function label(): string
    {
        return match ($this) {
            self::Complicated => '🥵 ' . __('Complicated'),
            self::Disappointing => '😕 ' . __('Disappointing'),
            self::Enriching => '🙌 ' . __('Enriching'),
            self::Frustrating => '😤 ' . __('Frustrating'),
            self::Good => '👍 ' . __('Good'),
            self::Great => '🎉 ' . __('Great'),
            self::Joyful => '😄 ' . __('Joyful'),
            self::Peaceful => '🧘 ' . __('Peaceful'),
            self::Productive => '🔥 ' . __('Productive'),
            self::Sad => '😢 ' . __('Sad'),
            self::Stimulating => '✨ ' . __('Stimulating'),
            self::Stressful => '⚡️ ' . __('Stressful'),
        };
    }

    public function category(): string
    {
        return match ($this) {
            self::Enriching,
            self::Good,
            self::Great,
            self::Joyful,
            self::Peaceful,
            self::Productive,
            self::Stimulating => 'pleasant',
            self::Complicated,
            self::Disappointing,
            self::Frustrating,
            self::Sad,
            self::Stressful => 'unpleasant',
        };
    }
}
