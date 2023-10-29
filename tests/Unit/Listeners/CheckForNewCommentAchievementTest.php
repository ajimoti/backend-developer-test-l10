<?php

namespace Tests\Unit\Listeners;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use App\Models\User;
use App\Events\AchievementUnlocked;
use App\Listeners\CheckForNewCommentAchievement;
use App\Events\CommentWritten;

class CheckForNewCommentAchievementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that AchievementUnlocked event is dispatched when a new comment achievement is unlocked
     */
    public function test_that_achievement_unlocked_event_is_dispatched_when_a_new_comment_achievement_is_unlocked(): void
    {
        Event::fake();

        // Create 5 comments and attach them to the user
        // 5 comments means the user has unlocked the Commenter achievement
        $user = User::factory()->hasComments(5)->create();

        $event = new CommentWritten($user->comments()->first());
        $listener = new CheckForNewCommentAchievement();
        $listener->handle($event);

        Event::assertDispatched(AchievementUnlocked::class);
    }

    /**
     * Test that AchievementUnlocked event is not dispatched when a new comment achievement is not unlocked
     */
    public function test_that_achievement_unlocked_event_is_not_dispatched_when_a_new_comment_achievement_is_not_unlocked(): void
    {
        Event::fake();

        // Create 4 comments and attach them to the user
        // 4 comments means the user has not unlocked the Commenter achievement
        $user = User::factory()->hasComments(4)->create();

        $event = new CommentWritten($user->comments()->first());
        $listener = new CheckForNewCommentAchievement();
        $listener->handle($event);

        Event::assertNotDispatched(AchievementUnlocked::class);
    }
}
