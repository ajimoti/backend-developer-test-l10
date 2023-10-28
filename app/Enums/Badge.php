<?php

namespace App\Enums;

use App\Contracts\AchieveableInterface;
use App\Concerns\UnlockableTrait;

enum Badge: int implements AchieveableInterface
{
    use UnlockableTrait;

    // ================================================================================
    // NOTE: For consistency, the badges should be listed in hierarchical order,
    // from the lowest to the highest.
    // ================================================================================

    case BEGINNER = 0;
    case INTERMEDIATE = 4;
    case ADVANCED = 8;
    case MASTER = 10;

    /*  Make an instance of the enum based on the given value.
     *
     *  @param integer $value
     *  @return self|null
     */
    public static function make(int $value): ?self
    {
        if ($value >= 0 && $value < 4) {
            return self::BEGINNER;
        }

        if ($value >= 4 && $value < 8) {
            return self::INTERMEDIATE;
        }

        if ($value >= 8 && $value < 10) {
            return self::ADVANCED;
        }

        if ($value >= 10) {
            return self::MASTER;
        }

        return null;
    }

    /*  Get the title of the badge.
     *
     *  @return string
     */
    public function getTitle(): string
    {
        return match ($this) {
            self::BEGINNER => 'Beginner',
            self::INTERMEDIATE => 'Intermediate',
            self::ADVANCED => 'Advanced',
            self::MASTER => 'Master',
        };
    }
}
