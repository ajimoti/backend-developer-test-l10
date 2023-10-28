<?php

namespace App\Collections;

use Illuminate\Support\Collection;
use App\Contracts\AchieveableInterface;

class AchievementCollection extends Collection
{
    /**
     * Convert a collection of AchieveableInterface to an array with other data.
     *
     * @param string|null $key (optional, only return the value of the given key)
     *
     * @return array
     */
    public function castToArray(?string $key = null): array
    {
        $result = $this->map(function ($achievement) {
            if (! $achievement instanceof AchieveableInterface) {
                Throw new \Exception('The collection must contain instances of ' . AchieveableInterface::class . '.');
            }

            return [
                'name' => $achievement->name,
                'title' => $achievement->getTitle(),
                'value' => $achievement->value,
                'remaining_to_unlock_next' => $achievement->getRemainingToUnlockNext(),
            ];
        })->toArray();

        if ($key) {
            return array_column($result, $key);
        }

        return $result;
    }
}
