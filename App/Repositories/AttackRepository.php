<?php

namespace Modules\MigraineDiary\App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\MigraineDiary\App\Models\Attack;

/**
 * Data access for migraine {@see Attack} records scoped to a single user.
 */
class AttackRepository
{
    /**
     * Find an attack by id for the given user.
     */
    public function findForUser(int $id, int $userId): ?Attack
    {
        return Attack::where('user_id', $userId)->find($id);
    }

    /**
     * Find an attack by id for the given user or fail with 404.
     */
    public function findOrFailForUser(int $id, int $userId): Attack
    {
        return Attack::forUser($userId)->findOrFail($id);
    }

    /**
     * Return the user's open attack (no end_time), if any.
     */
    public function getActiveAttackForUser(int $userId): ?Attack
    {
        return Attack::forUser($userId)
            ->whereNull('end_time')
            ->first();
    }

    /**
     * Paginated list of attacks for the user, newest first (via {@see Attack::scopeForUser}).
     *
     *
     * @return LengthAwarePaginator<int, Attack>
     */
    public function getUserAttacksPaginated(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Attack::forUser($userId)->paginate($perPage);
    }

    /**
     * Last N attacks for the user, ordered by start_time then id (newest first).
     *
     *
     * @return Collection<int, Attack>
     */
    public function getLastRecentAttacks(int $userId, int $limit = 10): Collection
    {
        return Attack::forUser($userId)
            ->orderBy('start_time', 'desc')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * All attacks for the user, newest first (via {@see Attack::scopeForUser}).
     *
     *
     * @return Collection<int, Attack>
     */
    public function getUserAttacks(int $userId): Collection
    {
        return Attack::forUser($userId)->get();
    }

    /**
     * Persist a new attack row.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Attack
    {
        return Attack::create($data);
    }

    /**
     * Delete the given attack record.
     */
    public function delete(Attack $attack): bool
    {
        return $attack->delete();
    }

    /**
     * Mark the attack as ended by setting end_time to now.
     */
    public function endAttack(Attack $attack): bool
    {
        return $attack->update(['end_time' => now()]);
    }

    /**
     * Update attack attributes.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(Attack $attack, array $data): bool
    {
        return $attack->update($data);
    }
}
