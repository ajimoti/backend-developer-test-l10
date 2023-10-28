<?php

namespace App\Concerns;

trait UnlockableTrait
{
    abstract public static function cases(): array;

    /**
     * Get all the unlocked achievements.
     *
     * @return array
     */
    public function getAllUnlocked(): array
    {
        $allAchievements = collect(static::cases());

        $unlocked = $allAchievements->filter(function ($achievement) {
            return $achievement->value <= $this->value;
        });

        return $unlocked->toArray();
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
