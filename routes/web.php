<?php

use Illuminate\Support\Facades\Route;
use Modules\MigraineDiary\App\Http\Controllers\Admin\MigraineDiaryAdminController;
use Modules\MigraineDiary\App\Http\Controllers\MigraineAttackController;
use Modules\MigraineDiary\App\Http\Controllers\MigraineDiaryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['web', 'auth'])
	->prefix('migraine-diary')
	->name('user.migraine-diary.')
	->group(function () {
		Route::resource('/', MigraineDiaryController::class)->names('resource');

		Route::resource('/attacks', MigraineAttackController::class)->names('attacks');

		Route::get('/translations', [MigraineDiaryController::class, 'getTranslations']);

		Route::post('/attacks/{id}/end', [MigraineAttackController::class, 'endAttack'])
			->where('id', '[0-9]+')
			->name('attacks.end');
	});

Route::middleware(['web', 'auth'])
	->prefix('admin/migraine-diary')
	->name('admin.migraine-diary.')
	->group(function () {
		Route::get('/', [MigraineDiaryAdminController ::class, 'index'])->name('index');

		Route::post('/{type}/store', [MigraineDiaryAdminController::class, 'store'])
			->where(['type' => 'symptoms|triggers|meds'])
			->name('store');

		Route::get('/{type}/{id}/edit', [MigraineDiaryAdminController::class, 'edit'])
			->where(['id' => '[0-9]+', 'type' => 'symptoms|triggers|meds'])
			->name('edit');

		Route::match(['post', 'put'], '/{type}/{id}/update', [MigraineDiaryAdminController::class, 'update'])
			->where(['id' => '[0-9]+', 'type' => 'symptoms|triggers|meds'])
			->name('update');

		Route::delete('/{type}/{id}', [MigraineDiaryAdminController::class, 'destroy'])
			->where(['id' => '[0-9]+', 'type' => 'symptoms|triggers|meds'])
			->name('destroy');
	});
