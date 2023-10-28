<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Enums\Badge;
use App\Events\BadgeUnlocked;

class CheckForNewBadge
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(AchievementUnlocked $event): void
    {
        $achievementName = $event->achievementName;
        $user = $event->user;

        $newBadge = Badge::tryFrom($user->unlockedAchievements()->count());

        if ($newBadge) {
            BadgeUnlocked::dispatch($newBadge->getTitle(), $user);
        }
    }
}
