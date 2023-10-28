<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Enums\Badges;
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

        $newBadge = Badges::tryFrom($user->unlockedAchievements()->count());

        if ($newBadge) {
            BadgeUnlocked::dispatch($newBadge->getTitle(), $user);
        }
    }
}
