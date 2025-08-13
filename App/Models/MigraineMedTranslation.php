<?php

namespace Modules\MigraineDiary\App\Models;

use Illuminate\Database\Eloquent\Model;

class MigraineMedTranslation extends Model
{
	protected $table = 'migraine_med_translations';

	protected $fillable = [
		'med_id',
		'locale',
		'name',
		'description'
	];

	public $timestamps = false;
}
