<?php

declare(strict_types=1);

namespace App\Enums;

enum GoalStatus: string
{
    case Unstarted = 'unstarted';
    case Started = 'started';
    case Completed = 'completed';
    case Canceled = 'canceled';

    public function label(): string
    {
        return __(ucfirst($this->value));
    }

    public function color(): string
    {
        return match ($this) {
            self::Unstarted => 'zinc',
            self::Started => 'sky',
            self::Completed => 'emerald',
            self::Canceled => 'red',
        };
    }
}
