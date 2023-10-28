<?php

namespace App\Listeners;

use App\Events\CommentWritten;
use App\Enums\CommentsWrittenAchievement;
use App\Events\AchievementUnlocked;

class CheckForNewCommentAchievement
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
    public function handle(CommentWritten $event): void
    {
        $comment = $event->comment;
        $user = $comment->user;

        $newAchievement = CommentsWrittenAchievement::tryFrom($user->comments()->count());

        if ($newAchievement) {
            AchievementUnlocked::dispatch($newAchievement->getTitle(), $user);
        }
    }
}
