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

    public function badgeColor(): string
    {
        return $this->category() === 'pleasant' ? 'sky' : 'amber';
    }

    public function chartColor(): string
    {
        return match ($this) {
            self::Enriching => 'text-emerald-500',
            self::Good => 'text-teal-500',
            self::Great => 'text-cyan-500',
            self::Joyful => 'text-sky-500',
            self::Peaceful => 'text-indigo-500',
            self::Productive => 'text-violet-500',
            self::Stimulating => 'text-fuchsia-500',
            self::Complicated => 'text-rose-500',
            self::Disappointing => 'text-orange-500',
            self::Frustrating => 'text-red-500',
            self::Sad => 'text-slate-500',
            self::Stressful => 'text-amber-500',
        };
    }

    public function chartLegendColor(): string
    {
        return match ($this) {
            self::Enriching => 'bg-emerald-500',
            self::Good => 'bg-teal-500',
            self::Great => 'bg-cyan-500',
            self::Joyful => 'bg-sky-500',
            self::Peaceful => 'bg-indigo-500',
            self::Productive => 'bg-violet-500',
            self::Stimulating => 'bg-fuchsia-500',
            self::Complicated => 'bg-rose-500',
            self::Disappointing => 'bg-orange-500',
            self::Frustrating => 'bg-red-500',
            self::Sad => 'bg-slate-500',
            self::Stressful => 'bg-amber-500',
        };
    }
}
