<?php

namespace App\Contracts;

interface AchieveableInterface
{
    /*  Make an instance of the enum based on the given value.
     *
     *  @param integer $value
     *  @return static|null
     */
    public static function make(int $value): ?static;

    /*  Get the title of the badge.
     *
     *  @return string
     */
    public function getTitle(): string;

    /**
     * Get all the unlocked achievements.
     *
     * @return array
     */
    public function getAllUnlocked(): array;

    /**
     * Get the next achievement.
     *
     * @return static|null
     */
    public function getNext(): ?static;
}
