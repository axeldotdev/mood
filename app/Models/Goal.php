<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\GoalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Goal extends Model
{
    /** @use HasFactory<\Database\Factories\GoalFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Attribute<GoalStatus, never>
     */
    protected function status(): Attribute
    {
        /** @var Attribute<GoalStatus, never> */
        return Attribute::get(fn (): GoalStatus => match (true) {
            $this->canceled_at !== null => GoalStatus::Canceled,
            $this->completed_at !== null => GoalStatus::Completed,
            $this->start_at !== null => GoalStatus::Started,
            default => GoalStatus::Unstarted,
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'start_at' => 'datetime',
            'completed_at' => 'datetime',
            'canceled_at' => 'datetime',
        ];
    }
}
