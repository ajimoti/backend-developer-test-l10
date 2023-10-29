<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Comment;
use App\Models\Lesson;
use App\Enums\LessonsWatchedAchievement;
use App\Enums\CommentsWrittenAchievement;
use App\Collections\AchievementCollection;
use App\Enums\Badge;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that user model has the correct fillable properties.
     */
    public function test_user_model_has_the_correct_fillable_properties(): void
    {
        $this->assertEquals([
            'name',
            'email',
            'password',
        ], (new \App\Models\User())->getFillable());
    }

    /**
     * Test that user model has the correct hidden properties.
     */
    public function test_user_model_has_the_correct_hidden_properties(): void
    {
        $this->assertEquals([
            'password',
            'remember_token',
        ], (new User())->getHidden());
    }

    /**
     * Test that the user model has the correct casts.
     */
    public function test_user_model_has_the_correct_casts(): void
    {
        $this->assertEquals([
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'id' => 'int',
        ], (new User())->getCasts());
    }

    /**
     * Test that the user model has the correct relationships.
     */
    public function test_user_model_has_the_correct_relationships(): void
    {
        $this->assertInstanceOf(HasMany::class, (new User())->comments());
        $this->assertInstanceOf(BelongsToMany::class, (new User())->lessons());
        $this->assertInstanceOf(BelongsToMany::class, (new User())->watched());
    }

    /**
     * Test that comments relationship returns the correct comments.
     */
    public function test_comments_relationship_returns_the_correct_comments(): void
    {
        $user = User::factory()->create();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(Comment::class, $user->comments->first());
        $this->assertEquals($comment->id, $user->comments->first()->id);
        $this->assertEquals($comment->user_id, $user->comments->first()->user_id);
        $this->assertEquals($comment->body, $user->comments->first()->body);
        $this->assertEquals(1, $user->comments()->count());
    }

    /**
     * Test that lessons relationship returns the correct lessons.
     */
    public function test_lessons_relationship_returns_the_correct_lessons(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $user->lessons()->attach($lesson);

        $this->assertInstanceOf(Lesson::class, $user->lessons->first());
        $this->assertEquals($lesson->id, $user->lessons->first()->id);
        $this->assertEquals($lesson->title, $user->lessons->first()->title);
        $this->assertEquals($lesson->body, $user->lessons->first()->body);
        $this->assertEquals(1, $user->lessons()->count());
    }

    /**
     * Test that watched relationship returns the correct lessons.
     */
    public function test_watched_relationship_returns_the_correct_lessons(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $user->lessons()->attach($lesson, ['watched' => true]);

        $this->assertInstanceOf(Lesson::class, $user->watched->first());
        $this->assertEquals($lesson->id, $user->watched->first()->id);
        $this->assertEquals($lesson->title, $user->watched->first()->title);
        $this->assertEquals($lesson->body, $user->watched->first()->body);
        $this->assertEquals(1, $user->watched()->count());
    }

    /**
     * Test that unwatched lessons are not included in the watched relationship.
     */
    public function test_unwatched_lessons_are_not_included_in_the_watched_relationship(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $user->lessons()->attach($lesson, ['watched' => false]);

        $this->assertEquals(0, $user->watched()->count());
    }

    /**
     * Test that the current lesson achievement method returns the correct achievement.
     */
    public function test_current_lesson_achievement_method_returns_the_correct_achievement(): void
    {
        $user = User::factory()->create();
        $this->assertNull($user->latestLessonAchievement());

        $lesson = Lesson::factory()->create();
        $user->lessons()->attach($lesson, ['watched' => true]);

        $this->assertEquals(LessonsWatchedAchievement::FIRST, $user->latestLessonAchievement());
        $this->assertInstanceOf(LessonsWatchedAchievement::class, $user->latestLessonAchievement());
    }

    /**
     * Test that the current lesson achievement method returns the correct achievement for first
     */
    public function test_current_lesson_achievement_method_returns_the_correct_for_first(): void
    {
        $user = User::factory()->create();
        $this->assertNull($user->latestLessonAchievement());

        foreach (range(1, 4) as $i) {
            $lesson = Lesson::factory()->create();
            $user->lessons()->attach($lesson, ['watched' => true]);

            $this->assertEquals(LessonsWatchedAchievement::FIRST, $user->latestLessonAchievement());
            $this->assertInstanceOf(LessonsWatchedAchievement::class, $user->latestLessonAchievement());
        }
    }

    /**
     * Test that the current lesson achievement method returns the correct achievement for fifth.
     */
    public function test_current_lesson_achievement_method_returns_the_correct_achievement_for_fifth(): void
    {
        $user = User::factory()->create();
        $this->assertNull($user->latestLessonAchievement());

        $lesson = Lesson::factory()->count(5)->create();
        $user->lessons()->attach($lesson, ['watched' => true]);

        foreach (range(6, 9) as $i) {
            $lesson = Lesson::factory()->create();
            $user->lessons()->attach($lesson, ['watched' => true]);
            $this->assertEquals(LessonsWatchedAchievement::FIFTH, $user->latestLessonAchievement());
            $this->assertInstanceOf(LessonsWatchedAchievement::class, $user->latestLessonAchievement());
        }
    }

    /**
     * Test that the current lesson achievement method returns the correct achievement for tenth.
     */
    public function test_current_lesson_achievement_method_returns_the_correct_achievement_for_tenth(): void
    {
        $user = User::factory()->create();
        $this->assertNull($user->latestLessonAchievement());

        $lesson = Lesson::factory()->count(10)->create();
        $user->lessons()->attach($lesson, ['watched' => true]);

        foreach (range(11, 24) as $i) {
            $lesson = Lesson::factory()->create();
            $user->lessons()->attach($lesson, ['watched' => true]);
            $this->assertEquals(LessonsWatchedAchievement::TENTH, $user->latestLessonAchievement());
            $this->assertInstanceOf(LessonsWatchedAchievement::class, $user->latestLessonAchievement());
        }
    }

    /**
     * Test that the current lesson achievement method returns the correct achievement for twenty fifth.
     */
    public function test_current_lesson_achievement_method_returns_the_correct_achievement_for_twenty_fifth(): void
    {
        $user = User::factory()->create();
        $this->assertNull($user->latestLessonAchievement());

        $lesson = Lesson::factory()->count(25)->create();
        $user->lessons()->attach($lesson, ['watched' => true]);

        foreach (range(26, 49) as $i) {
            $lesson = Lesson::factory()->create();
            $user->lessons()->attach($lesson, ['watched' => true]);
            $this->assertEquals(LessonsWatchedAchievement::TWENTY_FIFTH, $user->latestLessonAchievement());
            $this->assertInstanceOf(LessonsWatchedAchievement::class, $user->latestLessonAchievement());
        }
    }

    /**
     * Test that the current lesson achievement method returns the correct achievement for fiftieth.
     */
    public function test_current_lesson_achievement_method_returns_the_correct_achievement_for_fiftieth(): void
    {
        $user = User::factory()->create();
        $this->assertNull($user->latestLessonAchievement());

        $lesson = Lesson::factory()->count(50)->create();
        $user->lessons()->attach($lesson, ['watched' => true]);

        foreach (range(51, 79) as $i) {
            $lesson = Lesson::factory()->create();
            $user->lessons()->attach($lesson, ['watched' => true]);
            $this->assertEquals(LessonsWatchedAchievement::FIFTIETH, $user->latestLessonAchievement());
            $this->assertInstanceOf(LessonsWatchedAchievement::class, $user->latestLessonAchievement());
        }
    }

    /**
     * Test that the current lesson achievement method returns the correct achievement when the user has watched more than one lesson, and has already unlocked the highest achievement.
     */
    public function test_current_lesson_achievement_method_returns_the_correct_achievement_when_the_user_has_watched_more_than_one_lesson_and_has_already_unlocked_the_highest_achievement(): void
    {
        $user = User::factory()->create();
        $this->assertNull($user->latestLessonAchievement());

        $lessons = Lesson::factory()->count(80)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);

        $this->assertEquals(LessonsWatchedAchievement::FIFTIETH, $user->latestLessonAchievement());
        $this->assertInstanceOf(LessonsWatchedAchievement::class, $user->latestLessonAchievement());
    }

    /**
     * Test that the current comment achievement method returns the correct achievement.
     */
    public function test_current_comment_achievement_method_returns_the_correct_achievement(): void
    {
        $user = User::factory()->create();
        $this->assertNull($user->latestCommentAchievement());

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(CommentsWrittenAchievement::FIRST, $user->latestCommentAchievement());
        $this->assertInstanceOf(CommentsWrittenAchievement::class, $user->latestCommentAchievement());
    }

    /**
     * Test that the current comment achievement method returns the correct achievement when the user has written more than one comment.
     */
    public function test_current_comment_achievement_method_returns_the_correct_achievement_when_the_user_has_written_more_than_one_comment(): void
    {
        $user = User::factory()->create();
        $this->assertNull($user->latestCommentAchievement());

        $comments = Comment::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(CommentsWrittenAchievement::THIRD, $user->latestCommentAchievement());
        $this->assertInstanceOf(CommentsWrittenAchievement::class, $user->latestCommentAchievement());
    }

    /**
     * Test that the current comment achievement method returns the correct achievement when the user has written more than one comment, and has already unlocked the highest achievement.
     */
    public function test_current_comment_achievement_method_returns_the_correct_achievement_when_the_user_has_written_more_than_one_comment_and_has_already_unlocked_the_highest_achievement(): void
    {
        $user = User::factory()->create();
        $this->assertNull($user->latestCommentAchievement());

        $comments = Comment::factory()->count(20)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(CommentsWrittenAchievement::TWENTIETH, $user->latestCommentAchievement());
        $this->assertInstanceOf(CommentsWrittenAchievement::class, $user->latestCommentAchievement());
    }

    /**
     * Test that the unlocked lesson achievements method returns the achievement collection class
     */
    public function test_unlocked_achievements_method_returns_the_achievement_collection_class(): void
    {
        $user = User::factory()->create();
        $this->assertInstanceOf(AchievementCollection::class, $user->unlockedLessonAchievements());
    }

    /**
     * Test that the unlocked comment achievements method returns the achievement collection class
     */
    public function test_unlocked_comment_achievements_method_returns_the_achievement_collection_class(): void
    {
        $user = User::factory()->create();
        $this->assertInstanceOf(AchievementCollection::class, $user->unlockedCommentAchievements());
    }

    /**
     * Test that the unlocked lesson achievements method returns the correct achievements for first
     */
    public function test_unlocked_lesson_achievements_method_returns_the_correct_achievements_for_first(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();
        $user->lessons()->attach($lesson, ['watched' => true]);

        $this->assertInstanceOf(AchievementCollection::class, $user->unlockedLessonAchievements());
        $this->assertEquals(new AchievementCollection([LessonsWatchedAchievement::FIRST]), $user->unlockedLessonAchievements());
    }

    /**
     * Test that the unlocked lesson achievements method returns the correct achievements for fifth.
     */
    public function test_unlocked_lesson_achievements_method_returns_the_correct_achievements_for_fifth(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(5)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);

        $this->assertInstanceOf(AchievementCollection::class, $user->unlockedLessonAchievements());
        $this->assertEquals(new AchievementCollection([LessonsWatchedAchievement::FIRST, LessonsWatchedAchievement::FIFTH]), $user->unlockedLessonAchievements());
    }

    /**
     * Test that the unlocked lesson achievements method returns the correct achievements for tenth.
     */
    public function test_unlocked_lesson_achievements_method_returns_the_correct_achievements_for_tenth(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(10)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);

        $this->assertInstanceOf(AchievementCollection::class, $user->unlockedLessonAchievements());
        $this->assertEquals(new AchievementCollection([
            LessonsWatchedAchievement::FIRST,
            LessonsWatchedAchievement::FIFTH,
            LessonsWatchedAchievement::TENTH
        ]), $user->unlockedLessonAchievements());
    }

    /**
     * Test that the unlocked lesson achievements method returns the correct achievements for twenty fifth.
     */
    public function test_unlocked_lesson_achievements_method_returns_the_correct_achievements_for_twenty_fifth(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(25)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);

        $this->assertInstanceOf(AchievementCollection::class, $user->unlockedLessonAchievements());
        $this->assertEquals(new AchievementCollection([
            LessonsWatchedAchievement::FIRST,
            LessonsWatchedAchievement::FIFTH,
            LessonsWatchedAchievement::TENTH,
            LessonsWatchedAchievement::TWENTY_FIFTH
        ]), $user->unlockedLessonAchievements());
    }

    /**
     * Test that the unlocked comment achievements method returns the correct achievements for first.
     */
    public function test_unlocked_comment_achievements_method_returns_the_correct_achievement_for_first(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(AchievementCollection::class, $user->unlockedCommentAchievements());
        $this->assertEquals(new AchievementCollection([CommentsWrittenAchievement::FIRST]), $user->unlockedCommentAchievements());
    }

    /**
     * Test that the unlocked comment achievements method returns the correct achievements for third.
     */
    public function test_unlocked_comment_achievements_method_returns_the_correct_achievements_for_third(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(AchievementCollection::class, $user->unlockedCommentAchievements());
        $this->assertEquals(new AchievementCollection([
            CommentsWrittenAchievement::FIRST,
            CommentsWrittenAchievement::THIRD,
        ]), $user->unlockedCommentAchievements());
    }

    /**
     * Test that the unlocked comment achievements method returns the correct achievements for fifth.
     */
    public function test_unlocked_comment_achievements_method_returns_the_correct_achievements_for_fifth(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(AchievementCollection::class, $user->unlockedCommentAchievements());
        $this->assertEquals(new AchievementCollection([
            CommentsWrittenAchievement::FIRST,
            CommentsWrittenAchievement::THIRD,
            CommentsWrittenAchievement::FIFTH,
        ]), $user->unlockedCommentAchievements());
    }

    /**
     * Test that the unlocked comment achievements method returns the correct achievements for tenth.
     */
    public function test_unlocked_comment_achievements_method_returns_the_correct_achievements_for_tenth(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(10)->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(AchievementCollection::class, $user->unlockedCommentAchievements());
        $this->assertEquals(new AchievementCollection([
            CommentsWrittenAchievement::FIRST,
            CommentsWrittenAchievement::THIRD,
            CommentsWrittenAchievement::FIFTH,
            CommentsWrittenAchievement::TENTH,
        ]), $user->unlockedCommentAchievements());
    }

    /**
     * Test that the unlocked comment achievements method returns the correct achievements for twentieth.
     */
    public function test_unlocked_comment_achievements_method_returns_the_correct_achievements_for_twentieth(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(20)->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(AchievementCollection::class, $user->unlockedCommentAchievements());
        $this->assertEquals(new AchievementCollection([
            CommentsWrittenAchievement::FIRST,
            CommentsWrittenAchievement::THIRD,
            CommentsWrittenAchievement::FIFTH,
            CommentsWrittenAchievement::TENTH,
            CommentsWrittenAchievement::TWENTIETH,
        ]), $user->unlockedCommentAchievements());
    }

    /**
     * Test that the unlocked achievements method returns the correct achievements.
     */
    public function test_unlocked_achievements_method_returns_the_correct_achievements(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(6)->create();

        $user->lessons()->attach($lessons, ['watched' => true]);
        $comments = Comment::factory()->count(10)->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(AchievementCollection::class, $user->unlockedAchievements());
        $this->assertEquals(10, $user->comments()->count());
        $this->assertEquals(6, $user->watched()->count());
        $this->assertEquals(6, $user->lessons()->count());
        $this->assertEquals(new AchievementCollection([
            LessonsWatchedAchievement::FIRST,
            LessonsWatchedAchievement::FIFTH,
            CommentsWrittenAchievement::FIRST,
            CommentsWrittenAchievement::THIRD,
            CommentsWrittenAchievement::FIFTH,
            CommentsWrittenAchievement::TENTH,
        ]), $user->unlockedAchievements());
    }

    /**
     * Test that the unlocked achievements method returns the correct achievements when the user has unlocked the highest achievement.
     */
    public function test_unlocked_achievements_method_returns_the_correct_achievements_when_the_user_has_unlocked_the_highest_achievement(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(90)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $comments = Comment::factory()->count(90)->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(AchievementCollection::class, $user->unlockedAchievements());
        $this->assertEquals(90, $user->comments()->count());
        $this->assertEquals(90, $user->watched()->count());
        $this->assertEquals(90, $user->lessons()->count());
        $this->assertEquals(new AchievementCollection([
            LessonsWatchedAchievement::FIRST,
            LessonsWatchedAchievement::FIFTH,
            LessonsWatchedAchievement::TENTH,
            LessonsWatchedAchievement::TWENTY_FIFTH,
            LessonsWatchedAchievement::FIFTIETH,
            CommentsWrittenAchievement::FIRST,
            CommentsWrittenAchievement::THIRD,
            CommentsWrittenAchievement::FIFTH,
            CommentsWrittenAchievement::TENTH,
            CommentsWrittenAchievement::TWENTIETH,
        ]), $user->unlockedAchievements());
    }

    /**
     * Test that the next lesson achievement method returns the correct achievement.
     */
    public function test_next_lesson_achievement_method_returns_the_correct_achievement(): void
    {
        $user = User::factory()->create();
        $this->assertEquals(LessonsWatchedAchievement::FIRST, $user->nextAvailableLessonAchievement());

        $lessons = Lesson::factory()->count(1)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $this->assertEquals(LessonsWatchedAchievement::FIFTH, $user->nextAvailableLessonAchievement());

        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(5)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $this->assertEquals(LessonsWatchedAchievement::TENTH, $user->nextAvailableLessonAchievement());

        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(10)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $this->assertEquals(LessonsWatchedAchievement::TWENTY_FIFTH, $user->nextAvailableLessonAchievement());

        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(25)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $this->assertEquals(LessonsWatchedAchievement::FIFTIETH, $user->nextAvailableLessonAchievement());

        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(50)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $this->assertNull($user->nextAvailableLessonAchievement());
    }

    /**
     * Test that the next lesson achievement method returns the correct achievement when the user has unlocked the highest achievement.
     */
    public function test_next_lesson_achievement_method_returns_the_correct_achievement_when_the_user_has_unlocked_the_highest_achievement(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(90)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);

        $this->assertNull($user->nextAvailableLessonAchievement());
    }

    /**
     * Test that the next comment achievement method returns the correct achievement when the user has no comments.
     */
    public function test_next_comment_achievement_method_returns_the_correct_achievement_when_the_user_has_no_comments(): void
    {
        $user = User::factory()->create();
        $this->assertEquals(CommentsWrittenAchievement::FIRST, $user->nextAvailableCommentAchievement());
    }

    /**
     * Test that the next comment achievement method returns the correct achievement for first.
     */
    public function test_next_comment_achievement_method_returns_the_correct_achievement_for_first(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(1)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(CommentsWrittenAchievement::THIRD, $user->nextAvailableCommentAchievement());
    }

    /**
     * Test that the next comment achievement method returns the correct achievement for third.
     */
    public function test_next_comment_achievement_method_returns_the_correct_achievement_for_third(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(CommentsWrittenAchievement::FIFTH, $user->nextAvailableCommentAchievement());
    }

    /**
     * Test that the next comment achievement method returns the correct achievement for fifth.
     */
    public function test_next_comment_achievement_method_returns_the_correct_achievement_for_fifth(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(CommentsWrittenAchievement::TENTH, $user->nextAvailableCommentAchievement());
    }

    /**
     * Test that the next comment achievement method returns the correct achievement for tenth.
     */
    public function test_next_comment_achievement_method_returns_the_correct_achievement_for_tenth(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(10)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(CommentsWrittenAchievement::TWENTIETH, $user->nextAvailableCommentAchievement());
    }

    /**
     * Test that the next comment achievement method returns the correct achievement for twentieth.
     */
    public function test_next_comment_achievement_method_returns_the_correct_achievement_for_twentieth(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(20)->create([
            'user_id' => $user->id,
        ]);

        $this->assertNull($user->nextAvailableCommentAchievement());
    }

    /**
     * Test that the next comment achievement method returns the correct achievement when the user has unlocked the highest achievement.
     */
    public function test_next_comment_achievement_method_returns_the_correct_achievement_when_the_user_has_unlocked_the_highest_achievement(): void
    {
        $user = User::factory()->create();
        $comments = Comment::factory()->count(90)->create([
            'user_id' => $user->id,
        ]);

        $this->assertNull($user->nextAvailableCommentAchievement());
    }

    /**
     * Test that the next achievements method returns the correct achievements when the user has no achievements.
     */
    public function test_next_achievements_method_returns_the_correct_achievements_when_the_user_has_no_achievements(): void
    {
        $user = User::factory()->create();
        $this->assertEquals(new AchievementCollection([
            LessonsWatchedAchievement::FIRST,
            CommentsWrittenAchievement::FIRST,
        ]), $user->nextAvailableAchievements());
    }

    /**
     * Test that the next achievement method returns the correct achievements
     */
    public function test_next_achievement_method_returns_the_correct_achievements(): void
    {
        // ==============================================
        // First lesson watched, first comment written
        // ==============================================
        $user = User::factory()->create();
        $lessons = Lesson::factory()->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $comments = Comment::factory()->count(1)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(LessonsWatchedAchievement::FIRST, $user->latestLessonAchievement());
        $this->assertEquals(CommentsWrittenAchievement::FIRST, $user->latestCommentAchievement());
        $this->assertEquals(new AchievementCollection([
            LessonsWatchedAchievement::FIFTH,
            CommentsWrittenAchievement::THIRD,
        ]), $user->nextAvailableAchievements());


        // ==============================================
        // Fifth lesson watched, third comment written
        // ==============================================
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(5)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $comments = Comment::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(LessonsWatchedAchievement::FIFTH, $user->latestLessonAchievement());
        $this->assertEquals(CommentsWrittenAchievement::THIRD, $user->latestCommentAchievement());
        $this->assertEquals(new AchievementCollection([
            LessonsWatchedAchievement::TENTH,
            CommentsWrittenAchievement::FIFTH,
        ]), $user->nextAvailableAchievements());


        // ==============================================
        // Tenth lesson watched, fifth comment written
        // ==============================================
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(10)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $comments = Comment::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(LessonsWatchedAchievement::TENTH, $user->latestLessonAchievement());
        $this->assertEquals(CommentsWrittenAchievement::FIFTH, $user->latestCommentAchievement());
        $this->assertEquals(new AchievementCollection([
            LessonsWatchedAchievement::TWENTY_FIFTH,
            CommentsWrittenAchievement::TENTH,
        ]), $user->nextAvailableAchievements());

        // ==============================================
        // Twenty fifth lesson watched, tenth comment written
        // ==============================================
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(25)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $comments = Comment::factory()->count(10)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(LessonsWatchedAchievement::TWENTY_FIFTH, $user->latestLessonAchievement());
        $this->assertEquals(CommentsWrittenAchievement::TENTH, $user->latestCommentAchievement());
        $this->assertEquals(new AchievementCollection([
            LessonsWatchedAchievement::FIFTIETH,
            CommentsWrittenAchievement::TWENTIETH,
        ]), $user->nextAvailableAchievements());
    }

    /**
     * Test that the next achievement method returns the correct achievements when the user has unlocked the highest achievement.
     */
    public function test_next_achievement_method_returns_the_correct_achievements_when_the_user_has_unlocked_the_highest_achievement(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(90)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $comments = Comment::factory()->count(90)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(new AchievementCollection([]), $user->nextAvailableAchievements());
    }

    /**
     * Test that the badges method returns the correct badge for beginners
     */
    public function test_badges_method_returns_the_correct_badge_for_beginners(): void
    {
        $user = User::factory()->create();
        $this->assertEquals(Badge::BEGINNER, $user->badge());
    }

    /**
     * Test that the badges method returns the correct badge for intermediate users
     */
    public function test_badges_method_returns_the_correct_badge_for_intermediate_users(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(5)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $comments = Comment::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(Badge::INTERMEDIATE, $user->badge());
    }

    /**
     * Test that the badges method returns the correct badge for advanced users
     */
    public function test_badges_method_returns_the_correct_badge_for_advanced_users(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(25)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $comments = Comment::factory()->count(10)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(Badge::ADVANCED, $user->badge());
    }

    /**
     * Test that the badges method returns the correct badge for master users
     */
    public function test_badges_method_returns_the_correct_badge_for_master_users(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(50)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $comments = Comment::factory()->count(20)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(Badge::MASTER, $user->badge());
    }

    /**
     * Test that the badge method returns the correct badge for users who have unlocked all badges
     */
    public function test_badges_method_returns_the_correct_badge_for_users_who_have_unlocked_all_badges(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(90)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $comments = Comment::factory()->count(90)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(Badge::MASTER, $user->badge());
    }

    /**
     * Test that the next badge method returns the correct badge for beginners
     */
    public function test_next_badge_method_returns_the_correct_badge_for_beginners(): void
    {
        $user = User::factory()->create();
        $this->assertEquals(Badge::INTERMEDIATE, $user->nextBadge());
    }

    /**
     * Test that the next badge method returns the correct badge for intermediate users
     */
    public function test_next_badge_method_returns_the_correct_badge_for_intermediate_users(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(5)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $comments = Comment::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(Badge::ADVANCED, $user->nextBadge());
    }

    /**
     * Test that the next badge method returns the correct badge for advanced users
     */
    public function test_next_badge_method_returns_the_correct_badge_for_advanced_users(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(25)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $comments = Comment::factory()->count(10)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals(Badge::MASTER, $user->nextBadge());
    }

    /**
     * Test that the next badge method returns the correct badge for master users
     */
    public function test_next_badge_method_returns_the_correct_badge_for_master_users(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->count(50)->create();
        $user->lessons()->attach($lessons, ['watched' => true]);
        $comments = Comment::factory()->count(20)->create([
            'user_id' => $user->id,
        ]);

        $this->assertNull($user->nextBadge());
    }
}

