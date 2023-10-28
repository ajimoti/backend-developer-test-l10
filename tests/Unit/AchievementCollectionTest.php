<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Collections\AchievementCollection;
use App\Models\Lesson;
use Illuminate\Support\Collection;
use App\Enums\CommentsWrittenAchievement;
use App\Enums\LessonsWatchedAchievement;
use App\Contracts\AchieveableInterface;

class AchievementCollectionTest extends TestCase
{
    /**
     * Test that the achievement collection is an instance of collection.
     */
    public function test_is_instance_of_collection(): void
    {
        $this->assertInstanceOf(Collection::class, new AchievementCollection());
    }

    /**
     * Test that the cast to array method returns the correct array.
     */
    public function test_cast_to_array_method_returns_the_correct_array(): void
    {
        $collection = new AchievementCollection([
            CommentsWrittenAchievement::make(1),
            LessonsWatchedAchievement::make(1),
        ]);

        $this->assertEquals([
            [
                'name' => 'FIRST',
                'title' => 'First Comment Written',
                'value' => 1,
                'remaining_to_unlock_next' => 2,
            ],
            [
                'name' => 'FIRST',
                'title' => 'First Lesson Watched',
                'value' => 1,
                'remaining_to_unlock_next' => 4,
            ],
        ], $collection->castToArray());
    }

    /**
     * Test that the cast to array method returns the correct array when a key is given.
     */
    public function test_cast_to_array_method_returns_the_correct_array_when_a_key_is_given(): void
    {
        $collection = new AchievementCollection([
            CommentsWrittenAchievement::make(1),
            LessonsWatchedAchievement::make(1),
        ]);

        $this->assertEquals([
            'First Comment Written',
            'First Lesson Watched',
        ], $collection->castToArray('title'));
    }

    /**
     * Test that the cast to array method throws an exception when the collection contains invalid values.
     */
    public function test_cast_to_array_method_throws_an_exception_when_the_collection_contains_invalid_values(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The collection must contain instances of ' . AchieveableInterface::class . '.');

        $collection = new AchievementCollection([
            new Lesson(), 1, 'random_string'
        ]);

        $collection->castToArray();
    }

    /**
     * Test that the cast to array method throws an exception when the collection contains invalid values.
     */
    public function test_cast_to_array_method_throws_an_exception_when_the_collection_contains_atleast_one_invalid_values(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The collection must contain instances of ' . AchieveableInterface::class . '.');

        $collection = new AchievementCollection([
            CommentsWrittenAchievement::make(1),
            LessonsWatchedAchievement::make(1),
            new Lesson(),
        ]);

        $collection->castToArray();
    }
}
