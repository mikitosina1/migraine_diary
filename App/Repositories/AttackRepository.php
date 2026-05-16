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
	 *
	 * @param int $id
	 * @param int $userId
	 *
	 * @return Attack|null
	 */
	public function findForUser(int $id, int $userId): ?Attack
	{
		return Attack::where('user_id', $userId)->find($id);
	}

	/**
	 * Find an attack by id for the given user or fail with 404.
	 *
	 * @param int $id
	 * @param int $userId
	 *
	 * @return Attack
	 */
	public function findOrFailForUser(int $id, int $userId): Attack
	{
		return Attack::forUser($userId)->findOrFail($id);
	}

	/**
	 * Return the user's open attack (no end_time), if any.
	 *
	 * @param int $userId
	 *
	 * @return Attack|null
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
	 * @param int $userId
	 * @param int $perPage
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
	 * @param int $userId
	 * @param int $limit
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
	 * @param int $userId
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
	 * @param array<string, mixed> $data
	 *
	 * @return Attack
	 */
	public function create(array $data): Attack
	{
		return Attack::create($data);
	}

	/**
	 * Delete the given attack record.
	 *
	 * @param Attack $attack
	 *
	 * @return bool
	 */
	public function delete(Attack $attack): bool
	{
		return $attack->delete();
	}

	/**
	 * Mark the attack as ended by setting end_time to now.
	 *
	 * @param Attack $attack
	 *
	 * @return bool
	 */
	public function endAttack(Attack $attack): bool
	{
		return $attack->update(['end_time' => now()]);
	}

	/**
	 * Update attack attributes.
	 *
	 * @param Attack $attack
	 * @param array<string, mixed> $data
	 *
	 * @return bool
	 */
	public function update(Attack $attack, array $data): bool
	{
		return $attack->update($data);
	}
}
