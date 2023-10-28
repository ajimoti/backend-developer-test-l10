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
     * The lessons watched achievement.
     *
     * @return LessonsWatchedAchievement|null
     */
    public function lessonAchievement(): ?LessonsWatchedAchievement
    {
        $totalWatched = $this->watched()->count();

        return LessonsWatchedAchievement::make($totalWatched);
    }

    /**
     * The comments written achievement.
     *
     * @return CommentsWrittenAchievement|null
     */
    public function commentAchievement(): ?CommentsWrittenAchievement
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
        return $this->lessonAchievement()->getAllUnlocked();
    }

    /**
     * All the unlocked comment achievements.
     *
     * @return AchievementCollection
     */
    public function unlockedCommentAchievements(): AchievementCollection
    {
        return $this->commentAchievement()->getAllUnlocked();
    }

    /**
     * All the unlocked achievements.
     *
     * @return AchievementCollection
     */
    public function unlockedAchievements(): AchievementCollection
    {
        return $this->unlockedLessonAchievements()->merge($this->unlockedCommentAchievements());
    }

    /**
     * The next available lesson achievement.
     *
     * @return LessonsWatchedAchievement|null
     */
    public function nextLessonAchievement(): ?LessonsWatchedAchievement
    {
        return $this->lessonAchievement()->getNext();
    }

    /**
     * The next available comment achievement.
     *
     * @return CommentsWrittenAchievement|null
     */
    public function nextCommentAchievement(): ?CommentsWrittenAchievement
    {
        return $this->commentAchievement()->getNext();
    }

    /**
     * The next available achievements.
     *
     * @return AchievementCollection
     */
    public function nextAchievements(): AchievementCollection
    {
        return new AchievementCollection([
            $this->nextLessonAchievement(),
            $this->nextCommentAchievement()
        ]);
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
}

