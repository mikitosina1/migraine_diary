<?php

namespace Modules\MigraineDiary\App\Policies;

use App\Models\User;
use Modules\MigraineDiary\App\Models\Attack;

class AttackPolicy
{
    public function before(
        User $user,
        string $ability
    ): ?bool {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(
        User $user,
        Attack $attack
    ): bool {
        return $attack->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(
        User $user,
        Attack $attack
    ): bool {
        return $attack->user_id === $user->id;
    }

    public function delete(
        User $user,
        Attack $attack
    ): bool {
        return $attack->user_id === $user->id;
    }

    public function end(
        User $user,
        Attack $attack
    ): bool {
        return $attack->user_id === $user->id;
    }
}