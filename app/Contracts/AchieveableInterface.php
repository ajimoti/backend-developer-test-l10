<?php

namespace App\Contracts;

use App\Collections\AchievementCollection;

interface AchieveableInterface
{
    /*  Make an instance of the enum based on the given value.
     *
     *  @param integer $value
     *  @return self|null
     */
    public static function make(int $value): ?self;

    /*  Get the title of the badge.
     *
     *  @return string
     */
    public function getTitle(): string;

    /**
     * Get all the unlocked achievements.
     *
     * @return AchievementCollection
     */
    public function getAllUnlocked(): AchievementCollection;

    /**
     * Get the next achievement.
     *
     * @return self|null
     */
    public function getNext(): ?self;

    /**
     * Get the remaining value to unlock the next achievement.
     *
     * @return integer
     */
    public function getRemainingToUnlockNext(): int;
}
