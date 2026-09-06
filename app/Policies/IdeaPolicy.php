<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Idea;
use App\Models\User;

class IdeaPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, Idea $idea): bool
    {
        // يسمح بعرض الفكرة لمالكها فقط.
        return $user?->id === $idea->user_id;
    }

    public function create(?User $user): bool
    {
        return true;
    }

    public function update(?User $user, Idea $idea): bool
    {
        return true;
    }

    public function delete(?User $user, Idea $idea): bool
    {
        // يمنع حذف فكرة مستخدم آخر حتى لو عُرف رقمها.
        return $user?->id === $idea->user_id;
    }
}
