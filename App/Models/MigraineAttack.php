<?php

namespace Modules\MigraineDiary\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MigraineAttack extends Model
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
	 * return symptoms (many-to-many)
	 * @return BelongsToMany
	 */
	public function symptoms(): BelongsToMany
	{
		return $this->belongsToMany(
			MigraineSymptom::class,
			'migraine_attack_symptom'
		);
	}

	/**
	 * return triggers (many-to-many)
	 * @return BelongsToMany
	*/
	public function triggers(): BelongsToMany
	{
		return $this->belongsToMany(
			MigraineTrigger::class,
			'migraine_attack_trigger'
		);
	}

	/**
	 * return meds (many-to-many)
	 * @return BelongsToMany
	 */
	public function meds(): BelongsToMany
	{
		return $this->belongsToMany(
			MigraineMed::class,
			'migraine_attack_med'
		)->withPivot('dosage');
	}
}
