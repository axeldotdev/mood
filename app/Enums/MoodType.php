<?php

declare(strict_types=1);

namespace App\Enums;

enum MoodType: string
{
    case Accomplished = 'accomplished';
    case Angry = 'angry';
    case Anxious = 'anxious';
    case Bored = 'bored';
    case Complicated = 'complicated';
    case Confident = 'confident';
    case Confused = 'confused';
    case Content = 'content';
    case Disappointing = 'disappointing';
    case Energetic = 'energetic';
    case Enriching = 'enriching';
    case Excited = 'excited';
    case Exhausted = 'exhausted';
    case Focused = 'focused';
    case Frustrating = 'frustrating';
    case Good = 'good';
    case Grateful = 'grateful';
    case Great = 'great';
    case Hopeful = 'hopeful';
    case Inspired = 'inspired';
    case Irritated = 'irritated';
    case Joyful = 'joyful';
    case Lonely = 'lonely';
    case Loved = 'loved';
    case Overwhelmed = 'overwhelmed';
    case Peaceful = 'peaceful';
    case Productive = 'productive';
    case Restless = 'restless';
    case Sad = 'sad';
    case Stimulating = 'stimulating';
    case Stressful = 'stressful';
    case Worried = 'worried';

    public function emoji(): string
    {
        return match ($this) {
            self::Accomplished => '🏆',
            self::Angry => '😠',
            self::Anxious => '😰',
            self::Bored => '🥱',
            self::Complicated => '😵‍💫',
            self::Confident => '💪',
            self::Confused => '🫤',
            self::Content => '😌',
            self::Disappointing => '😕',
            self::Energetic => '🚀',
            self::Enriching => '🙌',
            self::Excited => '🤩',
            self::Exhausted => '😴',
            self::Focused => '🎯',
            self::Frustrating => '😤',
            self::Good => '👍',
            self::Grateful => '🙏',
            self::Great => '🎉',
            self::Hopeful => '🌱',
            self::Inspired => '💡',
            self::Irritated => '😒',
            self::Joyful => '😄',
            self::Lonely => '😔',
            self::Loved => '💕',
            self::Overwhelmed => '🤯',
            self::Peaceful => '🧘',
            self::Productive => '🔥',
            self::Restless => '😣',
            self::Sad => '😢',
            self::Stimulating => '✨',
            self::Stressful => '😫',
            self::Worried => '😟',
        };
    }

    public function label(): string
    {
        return $this->emoji() . ' ' . __(ucfirst($this->value));
    }

    public function category(): string
    {
        return match ($this) {
            self::Accomplished,
            self::Confident,
            self::Content,
            self::Energetic,
            self::Enriching,
            self::Excited,
            self::Focused,
            self::Good,
            self::Grateful,
            self::Great,
            self::Hopeful,
            self::Inspired,
            self::Joyful,
            self::Loved,
            self::Peaceful,
            self::Productive,
            self::Stimulating => 'pleasant',
            self::Angry,
            self::Anxious,
            self::Bored,
            self::Complicated,
            self::Confused,
            self::Disappointing,
            self::Exhausted,
            self::Frustrating,
            self::Irritated,
            self::Lonely,
            self::Overwhelmed,
            self::Restless,
            self::Sad,
            self::Stressful,
            self::Worried => 'unpleasant',
        };
    }

    public function badgeColor(): string
    {
        return $this->category() === 'pleasant' ? 'sky' : 'amber';
    }

    public function chartColor(): string
    {
        return match ($this) {
            self::Accomplished => 'text-amber-500',
            self::Angry => 'text-red-600',
            self::Anxious => 'text-orange-400',
            self::Bored => 'text-gray-400',
            self::Complicated => 'text-rose-500',
            self::Confident => 'text-orange-500',
            self::Confused => 'text-amber-400',
            self::Content => 'text-lime-500',
            self::Disappointing => 'text-orange-500',
            self::Energetic => 'text-yellow-500',
            self::Enriching => 'text-emerald-500',
            self::Excited => 'text-pink-500',
            self::Exhausted => 'text-slate-500',
            self::Focused => 'text-blue-500',
            self::Frustrating => 'text-red-500',
            self::Good => 'text-teal-500',
            self::Grateful => 'text-green-500',
            self::Great => 'text-cyan-500',
            self::Hopeful => 'text-emerald-400',
            self::Inspired => 'text-purple-500',
            self::Irritated => 'text-red-400',
            self::Joyful => 'text-sky-500',
            self::Lonely => 'text-indigo-400',
            self::Loved => 'text-rose-400',
            self::Overwhelmed => 'text-blue-400',
            self::Peaceful => 'text-indigo-500',
            self::Productive => 'text-violet-500',
            self::Restless => 'text-amber-600',
            self::Sad => 'text-slate-500',
            self::Stimulating => 'text-fuchsia-500',
            self::Stressful => 'text-amber-500',
            self::Worried => 'text-yellow-600',
        };
    }

    public function chartLegendColor(): string
    {
        return match ($this) {
            self::Accomplished => 'bg-amber-500',
            self::Angry => 'bg-red-600',
            self::Anxious => 'bg-orange-400',
            self::Bored => 'bg-gray-400',
            self::Complicated => 'bg-rose-500',
            self::Confident => 'bg-orange-500',
            self::Confused => 'bg-amber-400',
            self::Content => 'bg-lime-500',
            self::Disappointing => 'bg-orange-500',
            self::Energetic => 'bg-yellow-500',
            self::Enriching => 'bg-emerald-500',
            self::Excited => 'bg-pink-500',
            self::Exhausted => 'bg-slate-500',
            self::Focused => 'bg-blue-500',
            self::Frustrating => 'bg-red-500',
            self::Good => 'bg-teal-500',
            self::Grateful => 'bg-green-500',
            self::Great => 'bg-cyan-500',
            self::Hopeful => 'bg-emerald-400',
            self::Inspired => 'bg-purple-500',
            self::Irritated => 'bg-red-400',
            self::Joyful => 'bg-sky-500',
            self::Lonely => 'bg-indigo-400',
            self::Loved => 'bg-rose-400',
            self::Overwhelmed => 'bg-blue-400',
            self::Peaceful => 'bg-indigo-500',
            self::Productive => 'bg-violet-500',
            self::Restless => 'bg-amber-600',
            self::Sad => 'bg-slate-500',
            self::Stimulating => 'bg-fuchsia-500',
            self::Stressful => 'bg-amber-500',
            self::Worried => 'bg-yellow-600',
        };
    }
}
