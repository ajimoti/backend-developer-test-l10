<?php

namespace Tests\Unit\Listeners;

use App\Events\BadgeUnlocked;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Lesson;
use Illuminate\Support\Facades\Event;
use App\Events\AchievementUnlocked;
use App\Listeners\CheckForNewBadge;

class CheckForNewBadgeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that BadgeUnlocked event is dispatched when a new badge is unlocked
     */
    public function test_that_badge_unlocked_event_is_dispatched_when_a_new_badge_is_unlocked(): void
    {
        Event::fake();
        $user = User::factory()->create();

        // Create 25 lessons and attach them to the user
        // 25 watched lessons means the user has at least four achievements
        // which makes them eligible for the Intermediate badge
        $lessons = Lesson::factory()->count(25)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);

        $event = new AchievementUnlocked('some random title', $user);
        $listener = new CheckForNewBadge();
        $listener->handle($event);

        Event::assertDispatched(BadgeUnlocked::class);
    }

    /**
     * Test that BadgeUnlocked event is not dispatched when a new badge is not unlocked
     */
    public function test_that_badge_unlocked_event_is_not_dispatched_when_a_new_badge_is_not_unlocked(): void
    {
        Event::fake();
        $user = User::factory()->create();

        // Create 24 lessons and attach them to the user
        // 24 watched lessons means the user has at least four achievements
        // which makes them ineligible for the Intermediate badge
        $lessons = Lesson::factory()->count(24)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);

        $event = new AchievementUnlocked('some random title', $user);
        $listener = new CheckForNewBadge();
        $listener->handle($event);

        Event::assertNotDispatched(BadgeUnlocked::class);
    }
}
