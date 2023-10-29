<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use App\Events\LessonWatched;
use App\Listeners\CheckForNewLessonAchievement;

class LessonWatchedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the LessonWatched event triggers it listeners when dispatched
     */
    public function test_that_the_lesson_watched_event_triggers_it_listeners_when_dispatched(): void
    {
        Event::fake();

        Event::assertListening(
            LessonWatched::class,
            CheckForNewLessonAchievement::class
        );
    }
}
