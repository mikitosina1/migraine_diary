<?php

namespace Modules\MigraineDiary\App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\MigraineDiary\App\Models\Attack;

class AttackRepository
{
	public function findForUser(int $id, int $userId): Attack
	{
		return Attack::where('user_id', $userId)->find($id);
	}

	public function findOrFailForUser(int $id, int $userId): Attack
	{
		return Attack::forUser($userId)->findOrFail($id);
	}

	public function getActiveAttackForUser(int $userId): Attack
	{
		return Attack::where('user_id', $userId)
			->whereNull('end_time')
			->first();
	}

	public function getUserAttacksPaginated(int $userId, int $perPage = 15): LengthAwarePaginator
	{
		return Attack::forUser($userId)->paginate($perPage);
	}

	public function getUserAttacks(int $userId): Collection
	{
		return Attack::forUser($userId)->get();
	}

	public function create(array $data): Attack
	{
		return Attack::create($data);
	}

	public function delete(Attack $attack): bool
	{
		return $attack->delete();
	}

	public function endAttack(Attack $attack): bool
	{
		return $attack->update(['end_time' => now()]);
	}

	public function update(Attack $attack, array $data): bool
	{
		return $attack->update($data);
	}
}
