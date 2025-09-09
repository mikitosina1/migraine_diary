<?php

namespace Modules\MigraineDiary\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * UserSymptom Model
 *
 * Represents user-defined custom symptoms for migraine attacks.
 * Allows users to create personalized symptom entries beyond predefined options.
 *
 * @package Modules\MigraineDiary\App\Models
 *
 * @method static Builder|UserSymptom create(array $attributes = [])
 * @method static Builder|UserSymptom find($id, $columns = ['*'])
 * @method static Builder|UserSymptom findOrFail($id, $columns = ['*'])
 * @method static Builder|UserSymptom where($column, $operator = null, $value = null, $boolean = 'and')
 * @method static Builder|UserSymptom whereIn($column, $values, $boolean = 'and', $not = false)
 * @method static Builder|UserSymptom orderBy($column, $direction = 'asc')
 * @method static Collection|UserSymptom[] get($columns = ['*'])
 * @method static Collection|UserSymptom[] all($columns = ['*'])
 */
class UserSymptom extends Model
{
	protected $table = 'migraine_user_symptoms';

	protected $fillable = [
		'user_id',
		'name',
		'description'
	];

	/**
	 * Get all custom symptoms for a specific user
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
	 * Get the user that owns this custom symptom
	 *
	 * @return BelongsTo
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
