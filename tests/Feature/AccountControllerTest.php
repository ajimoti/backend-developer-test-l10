<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Lesson;
use App\Enums\Badge;
use App\Enums\LessonsWatchedAchievement;
use App\Enums\CommentsWrittenAchievement;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Returns 404 if user does not exist.
     */
    public function test_returns_404_if_user_does_not_exist(): void
    {
        $response = $this->get('/users/1');

        $response->assertStatus(404);
    }

    /**
     * Returns 200 if user exists.
     */
    public function test_returns_200_if_user_exists(): void
    {
        $user = User::factory()->create();

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);
    }

    /**
     * Can get all achievements for a user with no achievements.
     */
    public function test_can_get_all_user_achievements_with_no_achievements(): void
    {
        $user = User::factory()->create();

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertStatus(200)->assertJson([
            "unlocked_achievements" => [],
            "next_available_achievements" => [LessonsWatchedAchievement::FIRST->getTitle(), CommentsWrittenAchievement::FIRST->getTitle()],
            "current_badge" => Badge::BEGINNER->getTitle(),
            "next_badge" => Badge::INTERMEDIATE->getTitle(),
            "remaining_to_unlock_next_badge" => 4
        ]);
    }

    /**
     * Can get all achievements for a user with one achievement.
     */
    public function test_can_get_all_user_achievements_with_one_achievement(): void
    {
        $user = User::factory()->create();

        $lessons = Lesson::factory()->count(1)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertStatus(200)->assertJson([
            "unlocked_achievements" => [LessonsWatchedAchievement::FIRST->getTitle()],
            "next_available_achievements" => [LessonsWatchedAchievement::FIFTH->getTitle(), CommentsWrittenAchievement::FIRST->getTitle()],
            "current_badge" => Badge::BEGINNER->getTitle(),
            "next_badge" => Badge::INTERMEDIATE->getTitle(),
            "remaining_to_unlock_next_badge" => 3
        ]);
    }

    /**
     * Can get all achievements for a user with multiple achievements.
    */
    public function test_can_get_all_user_achievements_with_multiple_achievements(): void
    {
        $user = User::factory()->hasComments(25)->create();

        $lessons = Lesson::factory()->count(9)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertStatus(200)->assertJson([
            "unlocked_achievements" =>   [
                "First Lesson Watched",
                "5 Lessons Watched",
                "First Comment Written",
                "3 Comments Written",
                "5 Comments Written",
                "10 Comments Written",
                "20 Comments Written",
            ],
            "next_available_achievements" => ["10 Lessons Watched"],
            "current_badge" => "Intermediate",
            "next_badge" => "Advanced",
            "remaining_to_unlock_next_badge" => 1
        ]);
    }
}
