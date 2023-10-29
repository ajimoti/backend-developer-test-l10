<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AchievementsController extends Controller
{
    public function index(User $user)
    {
        return response()->json([
            'unlocked_achievements' => $user->unlockedAchievements()->castToArray('title'),
            'next_available_achievements' => $user->nextAvailableAchievements()->castToArray('title'),
            'current_badge' => $user->badge()->getTitle(),
            'next_badge' =>  $user->nextBadge()->getTitle(),
            'remaining_to_unlock_next_badge' => $user->getTotalAchievementsNeededToUnlockNextBadge()
        ]);
    }
}
