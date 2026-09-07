<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Step;
use App\Models\User;

class StepPolicy
{
    public function update(User $user, Step $step): bool
    {
        return $user->id === $step->idea->user_id;
    }
}
