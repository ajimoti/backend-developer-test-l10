<?php

namespace App\Listeners;

use App\Events\LessonWatched;
use App\Enums\LessonsWatchedAchievement;
use App\Events\AchievementUnlocked;

class CheckForNewLessonAchievement
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
    public function handle(LessonWatched $event): void
    {
        $lesson = $event->lesson;
        $user = $event->user;

        $newAchievement = LessonsWatchedAchievement::tryFrom($user->lessons()->count());

        if ($newAchievement) {
            AchievementUnlocked::dispatch($newAchievement->getTitle(), $user);
        }
    }
}
