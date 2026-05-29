<?php

namespace Modules\MigraineDiary\App\Services\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\MigraineDiary\App\Models\{Med, Symptom, Trigger};

/**
 * EntityService
 *
 * Service for managing entities in admin.
 * Handles the creation, updating, and completion of basic symptoms, triggers, medications.
 *
 * @package Modules\MigraineDiary\App\Services\Admin
 */
class EntityService
{

	/**
	 * Find an entity by type and ID.
	 * @param string $type
	 * @return mixed
	 */
	public function listEntities(string $type): Collection
	{
		/** @var Model $modelClass */
		$modelClass = $this->getModelByType($type);

		return $modelClass::query()
			->with('translations')
			->orderBy('code')
			->get();
	}

	public function listAll(): array
	{
		return [
			'symptoms' => $this->listEntities('symptoms'),
			'triggers' => $this->listEntities('triggers'),
			'meds' => $this->listEntities('meds'),
		];
	}

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
				'description' => $translation['description'] ?? null,
			]);
		}

		return $model->load('translations');
	}

	/**
	 * Update an existing entity of the specified type.
	 * @param string $type
	 * @param int $id
	 * @param mixed $data
	 * @return Model
	 */
	public function updateEntity(string $type, int $id, mixed $data): Model
	{
		/** @var $modelClass Symptom|Trigger|Med */
		$modelClass = $this->getModelByType($type);
		$model = $modelClass::findOrFail($id);

		$model->update(['code' => $data['code']]);

		foreach ($data['translations'] as $locale => $translation) {
			$model->translations()->updateOrCreate(
				['locale' => $locale],
				[
					'name' => $translation['name'],
					'description' => $translation['description'] ?? null
				]
			);
		}
		return $model->load('translations');
	}

	/**
	 * patch an existing entity of the specified type
	 * @param string $type
	 * @param int $id
	 * @param array $data
	 * @return Model
	 */
	public function patchEntity(string $type, int $id, array $data): Model
	{
		$model = $this->findEntity($type, $id);

		if (array_key_exists('code', $data)) {
			$model->update(['code' => $data['code']]);
		}

		foreach (($data['translations'] ?? []) as $locale => $translation) {
			$payload = [];

			if (array_key_exists('name', $translation)) {
				$payload['name'] = $translation['name'];
			}

			if (array_key_exists('description', $translation)) {
				$payload['description'] = $translation['description'];
			}

			if ($payload !== []) {
				$model->translations()->updateOrCreate(
					['locale' => $locale],
					$payload
				);
			}
		}

		return $model->load('translations');
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
