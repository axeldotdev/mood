<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MoodType;
use Illuminate\Database\Eloquent\Casts\AsEnumArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperMood
 */
final class Mood extends Model
{
    /** @use HasFactory<\Database\Factories\MoodFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'types' => AsEnumArrayObject::of(MoodType::class),
        ];
    }
}
