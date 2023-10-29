<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use App\Listeners\CheckForNewCommentAchievement;
use App\Events\CommentWritten;

class CommentWrittenTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the CommentWritten event triggers it listeners when dispatched
     */
    public function test_that_the_comment_written_event_triggers_it_listeners_when_dispatched(): void
    {
        Event::fake();

        Event::assertListening(
            CommentWritten::class,
            CheckForNewCommentAchievement::class
        );
    }
}
