<?php

namespace App\Concerns;

use App\Collections\AchievementCollection;

trait UnlockableTrait
{
    abstract public static function cases(): array;

    /**
     * Get all the unlocked achievements.
     *
     * @return AchievementCollection
     */
    public function getAllUnlocked(): AchievementCollection
    {
        $allAchievements = new AchievementCollection(static::cases());

        $unlocked = $allAchievements->filter(function ($achievement) {
            return $achievement->value <= $this->value;
        });

        return $unlocked;
    }

    /**
     * Get the next achievement.
     *
     * @return static|null
     */
    public function getNext(): ?static
    {
        $allAchievements = collect(static::cases());

        $next = $allAchievements->filter(function ($achievement) {
            return $achievement->value > $this->value;
        })->first();

        return $next;
    }
}
