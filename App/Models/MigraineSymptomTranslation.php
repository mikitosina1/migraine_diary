<?php

namespace Modules\MigraineDiary\App\Models;

use Illuminate\Database\Eloquent\Model;

class MigraineSymptomTranslation extends Model
{
	public $timestamps = false;
	protected $table = 'migraine_symptom_translations';
	protected $fillable = [
		'symptom_id',
		'locale',
		'name',
		'description'
	];
}
