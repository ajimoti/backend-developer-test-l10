<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class BadgeUnlocked
{
    use Dispatchable, SerializesModels;

    public $badgeName;
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct(string $badgeName, User $user)
    {
        //
    }
}
