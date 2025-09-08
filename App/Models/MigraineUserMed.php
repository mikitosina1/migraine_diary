<?php

namespace Modules\MigraineDiary\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MigraineUserMed extends Model
{
	protected $table = 'migraine_user_meds';

	protected $fillable = [
		'user_id',
		'name',
		'dosage',
		'description'
	];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public static function getForUser(int $userId)
	{
		return self::where('user_id', $userId)
			->orderBy('name')
			->get();
	}
}
