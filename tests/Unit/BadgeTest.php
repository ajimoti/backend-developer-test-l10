<?php

namespace Tests\Unit;

use App\Collections\AchievementCollection;
use PHPUnit\Framework\TestCase;
use App\Enums\Badge;
use App\Contracts\AchieveableInterface;

class BadgeTest extends TestCase
{
    /**
     * Test that the badge class is an instance of achievement interface.
     */
    public function test_badge_class_is_an_instance_of_achievement_interface(): void
    {
        foreach (Badge::cases() as $badge) {
            $this->assertInstanceOf(AchieveableInterface::class, $badge);
        }
    }

    /**
     * Test that the badge class is an enum.
     */
    public function test_badge_class_is_an_enum(): void
    {
        foreach (Badge::cases() as $badge) {
            $this->assertInstanceOf(Badge::class, $badge);
        }
    }

    /**
     * Test that badges are listed in hierarchical order.
     */
    public function test_that_badges_are_listed_in_hierarchical_order(): void
    {
        $badges = Badge::cases();

        foreach ($badges as $index => $badge) {
            if ($index === 0) {
                continue;
            }

            $this->assertTrue($badge->value > $badges[$index - 1]->value);
        }
    }

    /**
     * Test that the make method handles invalid values.
     */
    public function test_the_make_method_handles_invalid_values(): void
    {
        $this->assertNull(Badge::make(-1));
    }

    /**
     * Test that make returns the correct badge.
     */
    public function test_the_make_method_returns_the_correct_badge(): void
    {
        // BEGINNER
        foreach (range(0, 3) as $value) {
            $this->assertEquals(Badge::BEGINNER, Badge::make($value));
        }

        // INTERMEDIATE
        foreach (range(4, 7) as $value) {
            $this->assertEquals(Badge::INTERMEDIATE, Badge::make($value));
        }

        // ADVANCED
        foreach (range(8, 9) as $value) {
            $this->assertEquals(Badge::ADVANCED, Badge::make($value));
        }

        // MASTER
        foreach (range(10, 19) as $value) {
            $this->assertEquals(Badge::MASTER, Badge::make($value));
        }

        $this->assertEquals(Badge::MASTER, Badge::make(10000));
    }

    /**
     * Test that getTitle returns the correct title.
     */
    public function test_getTitle_returns_the_correct_title(): void
    {
        $this->assertEquals('Beginner', Badge::BEGINNER->getTitle());
        $this->assertEquals('Intermediate', Badge::INTERMEDIATE->getTitle());
        $this->assertEquals('Advanced', Badge::ADVANCED->getTitle());
        $this->assertEquals('Master', Badge::MASTER->getTitle());
    }

    /**
     * Test that getAllUnlocked returns the correct badges.
     */
    public function test_getAllUnlocked_returns_the_correct_achievements(): void
    {
        // BEGINNER
        $this->assertEquals(
            new AchievementCollection([Badge::BEGINNER]),
            Badge::BEGINNER->getAllUnlocked()
        );

        // INTERMEDIATE
        $this->assertEquals(
            new AchievementCollection([Badge::BEGINNER, Badge::INTERMEDIATE]),
            Badge::INTERMEDIATE->getAllUnlocked()
        );

        // ADVANCED
        $this->assertEquals(
            new AchievementCollection([Badge::BEGINNER, Badge::INTERMEDIATE, Badge::ADVANCED]),
            Badge::ADVANCED->getAllUnlocked()
        );

        // MASTER
        $this->assertEquals(
            new AchievementCollection([Badge::BEGINNER, Badge::INTERMEDIATE, Badge::ADVANCED, Badge::MASTER]),
            Badge::MASTER->getAllUnlocked()
        );
    }

    /**
     * Test that getNext returns the correct badge.
     */
    public function test_getNext_returns_the_correct_badge(): void
    {
        // BEGINNER
        $this->assertEquals(Badge::INTERMEDIATE, Badge::BEGINNER->getNext());

        // INTERMEDIATE
        $this->assertEquals(Badge::ADVANCED, Badge::INTERMEDIATE->getNext());

        // ADVANCED
        $this->assertEquals(Badge::MASTER, Badge::ADVANCED->getNext());

        // MASTER
        $this->assertNull(Badge::MASTER->getNext());
    }

    /**
     * Test that getRemainingToUnlockNext returns the correct value.
     */
    public function test_getRemainingToUnlockNext_returns_the_correct_value(): void
    {
        // BEGINNER
        $this->assertEquals(4, Badge::BEGINNER->getRemainingToUnlockNext());

        // INTERMEDIATE
        $this->assertEquals(4, Badge::INTERMEDIATE->getRemainingToUnlockNext());

        // ADVANCED
        $this->assertEquals(2, Badge::ADVANCED->getRemainingToUnlockNext());

        // MASTER
        $this->assertEquals(0, Badge::MASTER->getRemainingToUnlockNext());
    }

    /**
     * Test that the getRemainingToUnlockNext method returns 0 when there are no more badges to unlock.
     */
    public function test_getRemainingToUnlockNext_returns_zero_when_there_are_no_more_achievements_to_unlock(): void
    {
        $this->assertEquals(0, Badge::MASTER->getRemainingToUnlockNext());
    }

    /**
     * Test that getAllUnlocked returns an instance of AchievementCollection.
     */
    public function test_get_unlocked_badges_returns_an_instance_of_achievement_collection(): void
    {
        $this->assertInstanceOf(AchievementCollection::class, Badge::BEGINNER->getAllUnlocked());
    }
}
