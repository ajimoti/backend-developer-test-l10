<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\LessonsWatchedAchievement;
use App\Enums\CommentsWrittenAchievement;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\Badge;
use App\Collections\AchievementCollection;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The comments that belong to the user.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * The lessons that a user has access to.
     */
    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class);
    }

    /**
     * The lessons that a user has watched.
     */
    public function watched(): BelongsToMany
    {
        return $this->lessons()->wherePivot('watched', true);
    }

    /**
     * The last lesson achievement unlocked by the user.
     *
     * @return LessonsWatchedAchievement|null
     */
    public function latestLessonAchievement(): ?LessonsWatchedAchievement
    {
        $totalWatched = $this->watched()->count();

        return LessonsWatchedAchievement::make($totalWatched);
    }

    /**
     * The last comment achievement unlocked by the user.
     *
     * @return CommentsWrittenAchievement|null
     */
    public function latestCommentAchievement(): ?CommentsWrittenAchievement
    {
        $totalComments = $this->comments()->count();

        return CommentsWrittenAchievement::make($totalComments);
    }

    /**
     * All the unlocked lesson achievements.
     *
     * @return AchievementCollection
     */
    public function unlockedLessonAchievements(): AchievementCollection
    {
        $unlockedAchievements = $this->latestLessonAchievement()?->getAllUnlocked();

        return $unlockedAchievements ?? new AchievementCollection();
    }

    /**
     * All the unlocked comment achievements.
     *
     * @return AchievementCollection
     */
    public function unlockedCommentAchievements(): AchievementCollection
    {
        $unlockedAchievements = $this->latestCommentAchievement()?->getAllUnlocked();

        return $unlockedAchievements ?? new AchievementCollection();
    }

    /**
     * All achievements that has been unlocked by the user.
     *
     * @return AchievementCollection
     */
    public function unlockedAchievements(): AchievementCollection
    {
        $unlockedAchievements = $this->unlockedLessonAchievements()?->merge($this->unlockedCommentAchievements());

        return $unlockedAchievements ?? new AchievementCollection();
    }

    /**
     * The next available lesson achievement.
     *
     * @return LessonsWatchedAchievement|null
     */
    public function nextAvailableLessonAchievement(): ?LessonsWatchedAchievement
    {
        $currentAchievement = $this->latestLessonAchievement();

        if ($currentAchievement) {
            return $currentAchievement->getNext();
        }

        // If there are no unlocked achievements, return the first one.
        return LessonsWatchedAchievement::FIRST;
    }

    /**
     * The next available comment achievement.
     *
     * @return CommentsWrittenAchievement|null
     */
    public function nextAvailableCommentAchievement(): ?CommentsWrittenAchievement
    {
        $currentAchievement = $this->latestCommentAchievement();

        if ($currentAchievement) {
            return $currentAchievement->getNext();
        }

        // If there are no unlocked achievements, return the first one.
        return CommentsWrittenAchievement::FIRST;
    }

    /**
     * All available next achievements.
     *
     * @return AchievementCollection
     */
    public function nextAvailableAchievements(): AchievementCollection
    {
        $nextAvailableAchievements = [];

        if ($this->nextAvailableLessonAchievement()) {
            $nextAvailableAchievements[] = $this->nextAvailableLessonAchievement();
        }

        if ($this->nextAvailableCommentAchievement()) {
            $nextAvailableAchievements[] = $this->nextAvailableCommentAchievement();
        }

        return new AchievementCollection($nextAvailableAchievements);
    }

    /**
     * The user's current badge.
     *
     * @return Badge
     */
    public function badge(): Badge
    {
        $totalUnlockedAchievements = $this->unlockedAchievements()->count();

        return Badge::make($totalUnlockedAchievements);
    }

    /**
     * The next available badge.
     *
     * returns null if there are no more badges to unlock.
     *
     * @return Badge|null
     */
    public function nextBadge(): ?Badge
    {
        return $this->badge()->getNext();
    }

    /**
     * The total achievements needed to unlock the next badge.
     *
     * @return int
     */
    public function getTotalAchievementsNeededToUnlockNextBadge(): int
    {
        $nextBadge = $this->badge()->getNext();

        $totalUnlockedAchievements = $this->unlockedAchievements()->count();

        return $nextBadge->value - $totalUnlockedAchievements;
    }
}

