<?php

namespace Modules\MigraineDiary\App\Policies;

use App\Contracts\ModuleAuthorization;
use App\Models\User;
use Modules\MigraineDiary\App\Models\Attack;

class AttackPolicy
{
    private const string MODULE = 'migraine-diary';

    public function __construct(
        private readonly ModuleAuthorization $authorization,
    ) {}

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
        return $this->can($user, 'view');
    }

    public function view(
        User $user,
        Attack $attack
    ): bool {
        return $this->can($user, 'view')
            && $attack->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $this->can($user, 'create');
    }

    public function update(
        User $user,
        Attack $attack
    ): bool {
        return $this->can($user, 'update')
            && $attack->user_id === $user->id;
    }

    public function delete(
        User $user,
        Attack $attack
    ): bool {
        return $this->can($user, 'delete')
            && $attack->user_id === $user->id;
    }

    public function end(
        User $user,
        Attack $attack
    ): bool {
        return $this->can($user, 'update')
            && $attack->user_id === $user->id;
    }

    private function can(User $user, string $permission): bool
    {
        return $this->authorization->allows($user, self::MODULE, $permission);
    }
}
