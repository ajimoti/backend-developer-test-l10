<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use App\Models\User;
use App\Events\LessonWatched;
use App\Models\Lesson;
use App\Listeners\CheckForNewLessonAchievement;
use App\Events\AchievementUnlocked;

class CheckForNewLessonAchievementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that AchievementUnlocked event is dispatched when a new lesson achievement is unlocked
     */
    public function test_that_achievement_unlocked_event_is_dispatched_when_a_new_lesson_achievement_is_unlocked(): void
    {
        Event::fake();

        // Create 10 lessons and attach them to the user
        // 10 lessons means the user has unlocked the Lesson Learner achievement
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(10)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);

        $event = new LessonWatched($user->lessons()->first(), $user);
        $listener = new CheckForNewLessonAchievement();
        $listener->handle($event);

        Event::assertDispatched(AchievementUnlocked::class);
    }

    /**
     * Test that AchievementUnlocked event is not dispatched when a new lesson achievement is not unlocked
     */
    public function test_that_achievement_unlocked_event_is_not_dispatched_when_a_new_lesson_achievement_is_not_unlocked(): void
    {
        Event::fake();

        // Create 9 lessons and attach them to the user
        // 9 lessons means the user has not unlocked the Lesson Learner achievement
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(9)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);

        $event = new LessonWatched($user->lessons()->first(), $user);
        $listener = new CheckForNewLessonAchievement();
        $listener->handle($event);

        Event::assertNotDispatched(AchievementUnlocked::class);
    }
}
