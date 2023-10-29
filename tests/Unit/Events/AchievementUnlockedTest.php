<?php

namespace Tests\Unit\Events;

use Tests\TestCase;
use App\Events\AchievementUnlocked;
use Illuminate\Support\Facades\Event;
use App\Listeners\CheckForNewBadge;

class AchievementUnlockedTest extends TestCase
{
    /**
     * Test that the AchievementUnlocked event triggers it listeners when dispatched
     */
    public function test_that_the_achievement_unlocked_event_triggers_it_listeners_when_dispatched(): void
    {
        Event::fake();

        Event::assertListening(
            AchievementUnlocked::class,
            CheckForNewBadge::class
        );
    }
}
