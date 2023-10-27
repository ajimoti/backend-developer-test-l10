<?php

namespace App\Enums;

use App\Contracts\AchieveableInterface;
use App\Concerns\UnlockableTrait;

enum LessonsWatchedAchievement: int implements AchieveableInterface
{
    use UnlockableTrait;

    // ================================================================================
    // NOTE: For consistency, the badges should be listed in hierarchical order,
    // from the lowest to the highest.
    // ================================================================================

    case FIRST = 1;
    case FIFTH = 5;
    case TENTH = 10;
    case TWENTY_FIFTH = 25;
    case FIFTIETH = 50;

    /**
     * Make an instance of the enum based on the given value.
     *
     * @param integer $value
     * @return self|null
     */
    public static function make(int $value): ?self
    {
        if ($value >= 1 && $value < 5) {
            return self::FIRST;
        }

        if ($value >= 5 && $value < 10) {
            return self::FIFTH;
        }

        if ($value >= 10 && $value < 25) {
            return self::TENTH;
        }

        if ($value >= 25 && $value < 50) {
            return self::TWENTY_FIFTH;
        }

        if ($value >= 50) {
            return self::FIFTIETH;
        }

        return null;
    }

    /**
     * Get the title of the achievement.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return match ($this) {
            self::FIRST => 'First Lesson Watched',
            self::FIFTH => '5 Lessons Watched',
            self::TENTH => '10 Lessons Watched',
            self::TWENTY_FIFTH => '25 Lessons Watched',
            self::FIFTIETH => '50 Lessons Watched',
        };
    }
}
