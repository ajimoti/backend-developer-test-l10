<?php

namespace Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use App\Enums\CommentsWrittenAchievement;
use App\Collections\AchievementCollection;
use App\Contracts\AchieveableInterface;

class CommentsWrittenAchievementTest extends TestCase
{
    /**
     * Test that the comments written achievement class is an instance of achievement interface.
     */
    public function test_comments_written_achievement_class_is_an_instance_of_achievement_interface(): void
    {
        foreach (CommentsWrittenAchievement::cases() as $achievement) {
            $this->assertInstanceOf(AchieveableInterface::class, $achievement);
        }
    }

    /**
     * Test that the comments written achievement class is an enum.
     */
    public function test_comments_written_achievement_class_is_an_enum(): void
    {
        foreach (CommentsWrittenAchievement::cases() as $achievement) {
            $this->assertInstanceOf(CommentsWrittenAchievement::class, $achievement);
        }
    }

    /**
     * Test that the comments written achievements are listed in hierarchical order.
     */
    public function test_comments_written_achievements_are_listed_in_hierarchical_order(): void
    {
        $achievements = CommentsWrittenAchievement::cases();

        foreach ($achievements as $index => $achievement) {
            if ($index === 0) {
                continue;
            }

            $this->assertTrue($achievement->value > $achievements[$index - 1]->value);
        }
    }

    /**
     * Test that the make method handles invalid values.
     */
    public function test_that_the_make_method_handles_invalid_values(): void
    {
        $this->assertNull(CommentsWrittenAchievement::make(-1));
        $this->assertNull(CommentsWrittenAchievement::make(0));
    }

    /**
     * Test that make returns the correct achievement.
     */
    public function test_that_the_make_method_returns_the_correct_achievement(): void
    {
        // FIRST
        $this->assertEquals(CommentsWrittenAchievement::FIRST, CommentsWrittenAchievement::make(1));
        $this->assertEquals(CommentsWrittenAchievement::FIRST, CommentsWrittenAchievement::make(2));

        // THIRD
        $this->assertEquals(CommentsWrittenAchievement::THIRD, CommentsWrittenAchievement::make(3));
        $this->assertEquals(CommentsWrittenAchievement::THIRD, CommentsWrittenAchievement::make(4));

        // FIFTH
        foreach (range(5, 9) as $i) {
            $this->assertEquals(CommentsWrittenAchievement::FIFTH, CommentsWrittenAchievement::make($i));
        }

        // TENTH
        foreach (range(10, 19) as $i) {
            $this->assertEquals(CommentsWrittenAchievement::TENTH, CommentsWrittenAchievement::make($i));
        }

        // TWENTIETH
        foreach (range(20, 30) as $i) {
            $this->assertEquals(CommentsWrittenAchievement::TWENTIETH, CommentsWrittenAchievement::make($i));
        }

        $this->assertEquals(CommentsWrittenAchievement::TWENTIETH, CommentsWrittenAchievement::make(2500));
    }

    /**
     * Test that the getTitle method returns the correct title.
     */
    public function test_that_getTitle_returns_the_correct_title(): void
    {
        $this->assertEquals('First Comment Written', CommentsWrittenAchievement::FIRST->getTitle());
        $this->assertEquals('3 Comments Written', CommentsWrittenAchievement::THIRD->getTitle());
        $this->assertEquals('5 Comments Written', CommentsWrittenAchievement::FIFTH->getTitle());
        $this->assertEquals('10 Comments Written', CommentsWrittenAchievement::TENTH->getTitle());
        $this->assertEquals('20 Comments Written', CommentsWrittenAchievement::TWENTIETH->getTitle());
    }

    /**
     * Test that the getAllUnlocked method returns the correct achievements.
     */
    public function test_that_getAllUnlocked_returns_the_correct_achievements(): void
    {
        // FIRST
        $this->assertEquals(
            new AchievementCollection([CommentsWrittenAchievement::FIRST]),
            CommentsWrittenAchievement::FIRST->getAllUnlocked()
        );

        // THIRD
        $this->assertEquals(
            new AchievementCollection([CommentsWrittenAchievement::FIRST, CommentsWrittenAchievement::THIRD]),
            CommentsWrittenAchievement::THIRD->getAllUnlocked()
        );

        // FIFTH
        $this->assertEquals(
            new AchievementCollection([CommentsWrittenAchievement::FIRST, CommentsWrittenAchievement::THIRD, CommentsWrittenAchievement::FIFTH]),
            CommentsWrittenAchievement::FIFTH->getAllUnlocked()
        );

        // TENTH
        $this->assertEquals(
            new AchievementCollection([CommentsWrittenAchievement::FIRST, CommentsWrittenAchievement::THIRD, CommentsWrittenAchievement::FIFTH, CommentsWrittenAchievement::TENTH]),
            CommentsWrittenAchievement::TENTH->getAllUnlocked()
        );

        // TWENTIETH
        $this->assertEquals(
            new AchievementCollection([CommentsWrittenAchievement::FIRST, CommentsWrittenAchievement::THIRD, CommentsWrittenAchievement::FIFTH, CommentsWrittenAchievement::TENTH, CommentsWrittenAchievement::TWENTIETH]),
            CommentsWrittenAchievement::TWENTIETH->getAllUnlocked()
        );
    }

    /**
     * Test that the getNext method returns the correct achievement.
     */
    public function test_that_getNext_returns_the_correct_achievement(): void
    {
        // FIRST
        $this->assertEquals(CommentsWrittenAchievement::THIRD, CommentsWrittenAchievement::FIRST->getNext());

        // THIRD
        $this->assertEquals(CommentsWrittenAchievement::FIFTH, CommentsWrittenAchievement::THIRD->getNext());

        // FIFTH
        $this->assertEquals(CommentsWrittenAchievement::TENTH, CommentsWrittenAchievement::FIFTH->getNext());

        // TENTH
        $this->assertEquals(CommentsWrittenAchievement::TWENTIETH, CommentsWrittenAchievement::TENTH->getNext());

        // TWENTIETH
        $this->assertNull(CommentsWrittenAchievement::TWENTIETH->getNext());
    }

    /**
     * Test that getAllUnlocked returns an instance of AchievementCollection.
     */
    public function test_that_getAllUnlocked_returns_an_instance_of_AchievementCollection(): void
    {
        $this->assertInstanceOf(AchievementCollection::class, CommentsWrittenAchievement::FIRST->getAllUnlocked());
    }
}
