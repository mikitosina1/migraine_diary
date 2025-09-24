<?php

namespace Modules\MigraineDiary\App\Services\Admin;

use Illuminate\Database\Eloquent\Model;
use Modules\MigraineDiary\App\Models\{Symptom, Trigger, Med};

/**
 * AttackService
 *
 * Service for managing migraine attacks.
 * Handles the creation, updating, and completion of attacks,
 * as well as the synchronization of related data (symptoms, triggers, medications).
 *
 * @package Modules\MigraineDiary\App\Services\Admin
 */
class EntityService
{
	/**
	 * Find an entity by type and ID.
	 * @param string $type
	 * @param int $id
	 * @return mixed
	 */
	public function findEntity(string $type, int $id): mixed
	{
		/** @var $modelClass Symptom|Trigger|Med */
		$modelClass = $this->getModelByType($type);
		return $modelClass::with('translations')->findOrFail($id);
	}

	/**
	 * get model class by type
	 * @param string $type
	 * @return string
	 */
	private function getModelByType(string $type): string
	{
		$map = [
			'symptoms' => Symptom::class,
			'triggers' => Trigger::class,
			'meds'     => Med::class,
		];

		if (!isset($map[$type])) {
			abort(404, 'Unknown type');
		}

		return $map[$type];
	}

	/**
	 * Create a new entity of the specified type.
	 * @param string $type
	 * @param mixed $data
	 * @return Model
	 */
	public function createEntity(string $type, mixed $data): Model
	{
		/** @var $modelClass Symptom|Trigger|Med */
		$modelClass = $this->getModelByType($type);

		$model = new $modelClass();
		$model->code = $data['code'];

		$model->save();

		foreach ($data['translations'] as $locale => $translation) {
			$model->translations()->create([
				'locale' => $locale,
				'name' => $translation['name'],
			]);
		}
		return $model->load('translations');
	}

	/**
	 * Update an existing entity of the specified type.
	 * @param string $type
	 * @param int $id
	 * @param mixed $data
	 * @return bool
	 */
	public function updateEntity(string $type, int $id, mixed $data): bool
	{
		/** @var $modelClass Symptom|Trigger|Med */
		$modelClass = $this->getModelByType($type);
		$model = $modelClass::findOrFail($id);

		$model->update(['code' => $data['code']]);

		foreach ($data['translations'] as $locale => $translation) {
			$model->translations()
				->updateOrCreate(
					['locale' => $locale],
					['name' => $translation['name']]
				);
		}
		return true;
	}

	/**
	 * Delete an entity by type and ID.
	 * @param string $type
	 * @param int $id
	 * @return string
	 */
	public function deleteEntity(string $type, int $id): string
	{

		/** @var $model Symptom|Trigger|Med */
		$model = $this->getModelByType($type);
		$item = $model::findOrFail($id);
		$code = $item->code;
		$item->translations()->delete();
		$item->delete();
		return $code;
	}
}
