<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Flux\Flux;

final class SavePendingMoodAction
{
    public function execute(User $user): void
    {
        $pendingMood = session()->pull('pending_mood');

        if ($pendingMood === null) {
            return;
        }

        $hasAlreadySavedMoodToday = $user->moods()
            ->whereDate('created_at', today())
            ->exists();

        if ($hasAlreadySavedMoodToday) {
            Flux::toast(
                text: __('You have already saved a mood today.'),
                variant: 'warning',
            );

            return;
        }

        $user->moods()->create([
            'types' => $pendingMood['types'],
            'comment' => $pendingMood['comment'],
        ]);

        Flux::toast(
            text: __('Your mood has been saved!'),
            variant: 'success',
        );
    }
}
