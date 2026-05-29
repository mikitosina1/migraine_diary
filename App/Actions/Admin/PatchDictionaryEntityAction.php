<?php

namespace Modules\MigraineDiary\App\Actions\Admin;

use Illuminate\Database\Eloquent\Model;
use Modules\MigraineDiary\App\Services\Admin\EntityService;

class PatchDictionaryEntityAction
{
	/**
	 * @param EntityService $entities
	 */
	public function __construct(
		private readonly EntityService $entities,
	) {}

	/**
	 * @param string $type
	 * @param int $id
	 * @param array $data
	 * @return Model
	 */
	public function execute(string $type,int $id, array $data): Model
	{
		return $this->entities->patchEntity($type, $id, $data);
	}
}
