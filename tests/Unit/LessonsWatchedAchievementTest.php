<?php

namespace Tests\Unit;

use App\Collections\AchievementCollection;
use PHPUnit\Framework\TestCase;
use App\Enums\LessonsWatchedAchievement;
use App\Contracts\AchieveableInterface;

class LessonsWatchedAchievementTest extends TestCase
{
    /**
     * Test that the lesson watched achievement class is an instance of achievement interface.
     */
    public function test_lesson_watched_achievement_class_is_an_instance_of_achievement_interface(): void
    {
        foreach (LessonsWatchedAchievement::cases() as $achievement) {
            $this->assertInstanceOf(AchieveableInterface::class, $achievement);
        }
    }

   /**
     * Test that the badge class is an enum.
     */
    public function test_lesson_watched_achievement_class_is_an_enum(): void
    {
        foreach (LessonsWatchedAchievement::cases() as $achievement) {
            $this->assertInstanceOf(LessonsWatchedAchievement::class, $achievement);
        }
    }

    /**
     * Test that the lessons watched achievements are listed in hierarchical order.
     */
    public function test_lessons_watched_achievements_are_listed_in_hierarchical_order(): void
    {
        $achievements = LessonsWatchedAchievement::cases();

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
        $this->assertNull(LessonsWatchedAchievement::make(-1));
        $this->assertNull(LessonsWatchedAchievement::make(0));
    }

    /**
     * Test that make returns the correct achievement.
     */
    public function test_that_the_make_method_returns_the_correct_achievement(): void
    {
        // FIRST
        $this->assertEquals(LessonsWatchedAchievement::FIRST, LessonsWatchedAchievement::make(1));
        $this->assertEquals(LessonsWatchedAchievement::FIRST, LessonsWatchedAchievement::make(2));

        // FIFTH
        foreach (range(5, 9) as $i) {
            $this->assertEquals(LessonsWatchedAchievement::FIFTH, LessonsWatchedAchievement::make($i));
        }

        // TENTH
        foreach (range(10, 24) as $i) {
            $this->assertEquals(LessonsWatchedAchievement::TENTH, LessonsWatchedAchievement::make($i));
        }

        // TWENTY_FIFTH
        foreach (range(25, 49) as $i) {
            $this->assertEquals(LessonsWatchedAchievement::TWENTY_FIFTH, LessonsWatchedAchievement::make($i));
        }

        // FIFTIETH
        foreach (range(50, 69) as $i) {
            $this->assertEquals(LessonsWatchedAchievement::FIFTIETH, LessonsWatchedAchievement::make($i));
        }

        $this->assertEquals(LessonsWatchedAchievement::FIFTIETH, LessonsWatchedAchievement::make(5000));
    }

    /**
     * Test that the get title method returns the correct title.
     */
    public function test_that_the_get_title_method_returns_the_correct_title(): void
    {
        $this->assertEquals('First Lesson Watched', LessonsWatchedAchievement::FIRST->getTitle());
        $this->assertEquals('5 Lessons Watched', LessonsWatchedAchievement::FIFTH->getTitle());
        $this->assertEquals('10 Lessons Watched', LessonsWatchedAchievement::TENTH->getTitle());
        $this->assertEquals('25 Lessons Watched', LessonsWatchedAchievement::TWENTY_FIFTH->getTitle());
        $this->assertEquals('50 Lessons Watched', LessonsWatchedAchievement::FIFTIETH->getTitle());
    }

    /**
     * Test that the getAllUnlocked method returns the correct achievements.
     */
    public function test_that_getAllUnlocked_returns_the_correct_achievements(): void
    {
        // FIRST
        $this->assertEquals(
            new AchievementCollection([LessonsWatchedAchievement::FIRST]),
            LessonsWatchedAchievement::FIRST->getAllUnlocked()
        );

        // FIFTH
        $this->assertEquals(
            new AchievementCollection([LessonsWatchedAchievement::FIRST, LessonsWatchedAchievement::FIFTH]),
            LessonsWatchedAchievement::FIFTH->getAllUnlocked()
        );

        // TENTH
        $this->assertEquals(
            new AchievementCollection([LessonsWatchedAchievement::FIRST, LessonsWatchedAchievement::FIFTH, LessonsWatchedAchievement::TENTH]),
            LessonsWatchedAchievement::TENTH->getAllUnlocked()
        );

        // TWENTY_FIFTH
        $this->assertEquals(
            new AchievementCollection([LessonsWatchedAchievement::FIRST, LessonsWatchedAchievement::FIFTH, LessonsWatchedAchievement::TENTH, LessonsWatchedAchievement::TWENTY_FIFTH]),
            LessonsWatchedAchievement::TWENTY_FIFTH->getAllUnlocked()
        );

        // FIFTIETH
        $this->assertEquals(
            new AchievementCollection([LessonsWatchedAchievement::FIRST, LessonsWatchedAchievement::FIFTH, LessonsWatchedAchievement::TENTH, LessonsWatchedAchievement::TWENTY_FIFTH, LessonsWatchedAchievement::FIFTIETH]),
            LessonsWatchedAchievement::FIFTIETH->getAllUnlocked()
        );
    }

    /**
     * Test that the getNext method returns the correct achievement.
     */
    public function test_that_getNext_returns_the_correct_achievement(): void
    {
        // FIRST
        $this->assertEquals(LessonsWatchedAchievement::FIFTH, LessonsWatchedAchievement::FIRST->getNext());

        // FIFTH
        $this->assertEquals(LessonsWatchedAchievement::TENTH, LessonsWatchedAchievement::FIFTH->getNext());

        // TENTH
        $this->assertEquals(LessonsWatchedAchievement::TWENTY_FIFTH, LessonsWatchedAchievement::TENTH->getNext());

        // TWENTY_FIFTH
        $this->assertEquals(LessonsWatchedAchievement::FIFTIETH, LessonsWatchedAchievement::TWENTY_FIFTH->getNext());

        // FIFTIETH
        $this->assertNull(LessonsWatchedAchievement::FIFTIETH->getNext());
    }

    /**
     * Test that getAllUnlocked returns an instance of AchievementCollection.
     */
    public function test_that_getAllUnlocked_returns_an_instance_of_AchievementCollection(): void
    {
        $this->assertInstanceOf(AchievementCollection::class, LessonsWatchedAchievement::FIRST->getAllUnlocked());
    }
}
