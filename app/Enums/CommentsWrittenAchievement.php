<?php

namespace App\Enums;

use App\Contracts\AchieveableInterface;
use App\Concerns\UnlockableTrait;

enum CommentsWrittenAchievement: int implements AchieveableInterface
{
    use UnlockableTrait;

    // ================================================================================
    // NOTE: For consistency, the badges should be listed in hierarchical order,
    // from the lowest to the highest.
    // ================================================================================

    case FIRST = 1;
    case THREE = 3;
    case FIFTH = 5;
    case TENTH = 10;
    case TWENTIETH = 20;

    /**
     * Make an instance of the enum based on the given value.
     *
     * @param integer $value
     * @return self|null
     */
    public static function make(int $value): ?self
    {
        if ($value >= 1 && $value < 3) {
            return self::FIRST;
        }

        if ($value >= 3 && $value < 5) {
            return self::THREE;
        }

        if ($value >= 5 && $value < 10) {
            return self::FIFTH;
        }

        if ($value >= 10 && $value < 20) {
            return self::TENTH;
        }

        if ($value >= 20) {
            return self::TWENTIETH;
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
            self::FIRST => 'First Comment Written',
            self::THREE => '3 Comments Written',
            self::FIFTH => '5 Comments Written',
            self::TENTH => '10 Comments Written',
            self::TWENTIETH => '20 Comments Written',
        };
    }
}
