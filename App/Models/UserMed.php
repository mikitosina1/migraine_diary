<?php

namespace Modules\MigraineDiary\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * UserMed Model
 *
 * Represents user-defined custom medications for migraine attacks.
 * Allows users to create personalized medication entries beyond predefined options.
 *
 * @package Modules\MigraineDiary\App\Models
 *
 * @method static Builder|UserMed create(array $attributes = [])
 * @method static Builder|UserMed find($id, $columns = ['*'])
 * @method static Builder|UserMed findOrFail($id, $columns = ['*'])
 * @method static Builder|UserMed where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder|UserMed whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static Builder|UserMed orderBy($column, $direction = 'asc')
 * @method static Collection|UserMed[] get($columns = ['*'])
 * @method static Collection|UserMed[] all($columns = ['*'])
 */
class UserMed extends Model
{
	protected $table = 'migraine_user_meds';

	protected $fillable = [
		'user_id',
		'name',
		'dosage',
		'description'
	];

	/**
	 * Get all custom medications for a specific user
	 *
	 * @param int $userId
	 * @return Collection
	 */
	public static function getForUser(int $userId)
	{
		return self::where('user_id', $userId)
			->orderBy('name')
			->get();
	}

	/**
	 * Get the user that owns this custom medication
	 * @return BelongsTo
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
