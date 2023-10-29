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
     * The current lessons watched achievement.
     *
     * @return LessonsWatchedAchievement|null
     */
    public function currentLessonAchievement(): ?LessonsWatchedAchievement
    {
        $totalWatched = $this->watched()->count();

        return LessonsWatchedAchievement::make($totalWatched);
    }

    /**
     * The current comments written achievement.
     *
     * @return CommentsWrittenAchievement|null
     */
    public function currentCommentAchievement(): ?CommentsWrittenAchievement
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
        $unlockedAchievements = $this->currentLessonAchievement()?->getAllUnlocked();

        return $unlockedAchievements ?? new AchievementCollection();
    }

    /**
     * All the unlocked comment achievements.
     *
     * @return AchievementCollection
     */
    public function unlockedCommentAchievements(): AchievementCollection
    {
        $unlockedAchievements = $this->currentCommentAchievement()?->getAllUnlocked();

        return $unlockedAchievements ?? new AchievementCollection();
    }

    /**
     * All the unlocked achievements.
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
    public function nextLessonAchievement(): ?LessonsWatchedAchievement
    {
        $currentAchievement = $this->currentLessonAchievement();

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
    public function nextCommentAchievement(): ?CommentsWrittenAchievement
    {
        $currentAchievement = $this->currentCommentAchievement();

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
    public function nextAchievements(): AchievementCollection
    {
        $nextAchievements = [];

        if ($this->nextLessonAchievement()) {
            $nextAchievements[] = $this->nextLessonAchievement();
        }

        if ($this->nextCommentAchievement()) {
            $nextAchievements[] = $this->nextCommentAchievement();
        }

        return new AchievementCollection($nextAchievements);
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
     * @return Badge|null
     */
    public function nextBadge(): ?Badge
    {
        return $this->badge()->getNext();
    }

    public function getTotalAchievementsNeededToUnlockNextBadge(): int
    {
        $nextBadge = $this->badge()->getNext();

        $totalUnlockedAchievements = $this->unlockedAchievements()->count();

        return $nextBadge->value - $totalUnlockedAchievements;
    }
}

