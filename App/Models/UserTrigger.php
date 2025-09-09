<?php

namespace Modules\MigraineDiary\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * UserTrigger Model
 *
 * Represents user-defined custom triggers for migraine attacks.
 * Allows users to create personalized trigger entries beyond predefined options.
 *
 * @package Modules\MigraineDiary\App\Models
 *
 * @method static Builder|UserTrigger create(array $attributes = [])
 * @method static Builder|UserTrigger find($id, $columns = ['*'])
 * @method static Builder|UserTrigger findOrFail($id, $columns = ['*'])
 * @method static Builder|UserTrigger where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder|UserTrigger whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static Builder|UserTrigger orderBy($column, $direction = 'asc')
 * @method static Collection|UserTrigger[] get($columns = ['*'])
 * @method static Collection|UserTrigger[] all($columns = ['*'])
 */
class UserTrigger extends Model
{
	protected $table = 'migraine_user_triggers';

	protected $fillable = [
		'user_id',
		'name',
		'description'
	];

	/**
	 * Get all custom triggers for a specific user
	 *
	 * @param int $userId
	 * @return Collection
	 */
	public static function getForUser(int $userId): Collection
	{
		return self::where('user_id', $userId)
			->orderBy('name')
			->get();
	}

	/**
	 * Get the user that owns this custom trigger
	 * @return BelongsTo
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
