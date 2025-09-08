<?php

namespace Modules\MigraineDiary\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MigraineUserSymptom extends Model
{
	protected $table = 'migraine_user_symptoms';

	protected $fillable = [
		'user_id',
		'name',
		'description'
	];

	/**
	 * Relationship with a user
	 */
	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * Get user's custom symptoms
	 */
	public static function getForUser(int $userId)
	{
		return self::where('user_id', $userId)
			->orderBy('name')
			->get();
	}
}
