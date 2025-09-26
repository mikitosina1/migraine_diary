<?php

namespace Modules\MigraineDiary\App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Attack Model
 *
 * Represents a migraine attack record with symptoms, triggers, and medications.
 * Handles both predefined and user-defined entries.
 *
 * @package Modules\MigraineDiary\App\Models
 *
 * @method static Builder|Attack create(array $attributes = [])
 * @method static Builder|Attack find($id, $columns = ['*'])
 * @method static Builder|Attack findOrFail($id, $columns = ['*'])
 * @method static Builder|Attack where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder|Attack whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static Builder|Attack orderBy($column, $direction = 'asc')
 * @method static Collection|Attack[] get($columns = ['*'])
 * @method static Collection|Attack[] all($columns = ['*'])
 */
class Attack extends Model
{
	protected $table = 'migraine_attacks';

	protected $fillable = [
		'user_id',
		'start_time',
		'end_time',
		'pain_level',
		'notes'
	];

	protected $casts = [
		'start_time' => 'datetime',
		'end_time' => 'datetime',
	];

	/**
	 * Method for getting attacks for a specific user
	 * using the scope method for getting attacks
	 *
	 * @use self::scopeForUser()
	 * @param int $userId
	 * @return Collection
	 */
	public static function getForUser(int $userId): Collection
	{
		return self::forUser($userId)->get();
	}

	/**
	 * return symptoms (many-to-many)
	 * @return BelongsToMany
	 */
	public function symptoms(): BelongsToMany
	{
		return $this->belongsToMany(
			Symptom::class,
			'migraine_attack_symptom',
			'attack_id',
			'symptom_id'
		);
	}

	/**
	 * return triggers (many-to-many)
	 * @return BelongsToMany
	*/
	public function triggers(): BelongsToMany
	{
		return $this->belongsToMany(
			Trigger::class,
			'migraine_attack_trigger',
			'attack_id',
			'trigger_id'
		);
	}

	/**
	 * return meds (many-to-many)
	 * @return BelongsToMany
	 */
	public function meds(): BelongsToMany
	{
		return $this->belongsToMany(
			Med::class,
			'migraine_attack_med',
			'attack_id',
			'med_id'
		)->withPivot('dosage');
	}

	public function userSymptoms(): BelongsToMany
	{
		return $this->belongsToMany(
			UserSymptom::class,
			'migraine_attack_user_symptom',
			'attack_id',
			'user_symptom_id'
		);
	}

	public function userTriggers(): BelongsToMany
	{
		return $this->belongsToMany(
			UserTrigger::class,
			'migraine_attack_user_trigger',
			'attack_id',
			'user_trigger_id'
		);
	}

	public function userMeds(): BelongsToMany
	{
		return $this->belongsToMany(
			UserMed::class,
			'migraine_attack_user_med',
			'attack_id',
			'user_med_id'
		)->withPivot('dosage');
	}

	/**
	 * Scope for getting attacks for a specific user
	 *
	 * @param Builder $query
	 * @param int $userId
	 * @return Builder
	 */
	public function scopeForUser(Builder $query, int $userId): Builder
	{
		return $query->where('user_id', $userId)
			->with(['symptoms', 'triggers', 'meds', 'userSymptoms', 'userTriggers', 'userMeds'])
			->orderBy('start_time', 'desc');
	}
}
