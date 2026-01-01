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

    public function emoji(): string
    {
        return match ($this) {
            self::Complicated => '🥵',
            self::Disappointing => '😕',
            self::Enriching => '🙌',
            self::Frustrating => '😤',
            self::Good => '👍',
            self::Great => '🎉',
            self::Joyful => '😄',
            self::Peaceful => '🧘',
            self::Productive => '🔥',
            self::Sad => '😢',
            self::Stimulating => '✨',
            self::Stressful => '⚡️',
        };
    }

    public function label(): string
    {
        return $this->emoji() . ' ' . __(ucfirst($this->value));
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

    public function color(): string
    {
        return $this->category() === 'pleasant' ? 'sky' : 'amber';
    }
}
