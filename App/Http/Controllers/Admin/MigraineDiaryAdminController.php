<?php

namespace Modules\MigraineDiary\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\MigraineDiary\App\Models\MigraineMed;
use Modules\MigraineDiary\App\Models\MigraineSymptom;
use Modules\MigraineDiary\App\Models\MigraineTrigger;

/**
 * Class MigraineDiaryAdminController
 *
 * @package Modules\MigraineDiary\App\Http\Controllers\Admin
 */
class MigraineDiaryAdminController extends Controller
{
	/**
	 * Display a listing of the resource.
	 * @return View|Factory|Application
	 */
	public function index(): View|Factory|Application
	{
		return view('migrainediary::admin.index-admin', [
			'symptomList' => MigraineSymptom::getListWithTranslations(),
			'triggerList' => MigraineTrigger::getListWithTranslations(),
			'medsList' => MigraineMed::getListWithTranslations(),
			'locales' => config('app.locales')
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 * @return View|Factory|Application
	 */
	public function create(): View|Factory|Application
	{
		return view('migrainediary::create');
	}

	/**
	 * Show the specified resource.
	 * @param int $id
	 * @return View|Factory|Application
	 */
	public function show(int $id): View|Factory|Application
	{
		return view('migrainediary::show');
	}

	/**
	 * Show the data for editing the specified resource.
	 * @param string $type type of model
	 * @param int $id id of record
	 * @return JsonResponse
	 */
	public function edit(string $type, int $id): JsonResponse
	{
		/** @var $modelClass MigraineSymptom|MigraineTrigger|MigraineMed */
		$modelClass = $this->getModelByType($type);

		$model = $modelClass::with('translations')->findOrFail($id);

		return response()->json($model);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @param string $type type of model
	 * @return JsonResponse
	 */
	public function store(Request $request, string $type): JsonResponse
	{
		$data = $request->validate([
			'code' => 'required|string|max:255',
			'translations' => 'required|array',
			'translations.*.name' => 'required|string|max:255',
		]);

		/** @var $modelClass MigraineSymptom|MigraineTrigger|MigraineMed */
		$modelClass = $this->getModelByType($type);

		$model = new $modelClass();
		$model->code = $data['code'];
		$model->save();

		foreach ($data['translations'] as $locale => $translation) {
			$model->translations()->create([
				'locale' => $locale,
				'name' => $translation['name'],
			]);
		}

		return response()->json([
			'success' => true,
			'item' => $model->load('translations'),
		]);
	}

	/**
	 * Update the specified resource in storage.
	 * @param Request $request
	 * @param string $type type of model
	 * @param int $id id of record
	 * @return JsonResponse
	 */
	public function update(Request $request, string $type, int $id): JsonResponse
	{
		$data = $request->validate([
			'code' => 'required|string|max:255',
			'translations' => 'required|array',
			'translations.*.name' => 'required|string|max:255',
		]);

		/** @var $modelClass MigraineSymptom|MigraineTrigger|MigraineMed */
		$modelClass = $this->getModelByType($type);
		$model = $modelClass::findOrFail($id);

		$model->update(['code' => $data['code']]);

		foreach ($data['translations'] as $locale => $translation) {
			$model->translations()
				->updateOrCreate(
					['locale' => $locale],
					['name' => $translation['name']]
				);
		}

		return response()->json(['success' => true]);
	}

	/**
	 * Remove the specified resource from storage.
	 * @param string $type
	 * @param int $id
	 * @return JsonResponse
	 */
	public function destroy(string $type, int $id): JsonResponse
	{
		/** @var $model MigraineSymptom|MigraineTrigger|MigraineMed */
		$model = $this->getModelByType($type);
		$item = $model::findOrFail($id);
		$code = $item->code;
		$item->translations()->delete();
		$item->delete();

		return response()->json(['success' => true, 'message' => 'Record ' . $code . ' deleted successfully']);
	}

	/**
	 * get model class by type
	 * @param string $type
	 * @return string
	 */
	private function getModelByType(string $type): string
	{
		$map = [
			'symptoms' => MigraineSymptom::class,
			'triggers' => MigraineTrigger::class,
			'meds'     => MigraineMed::class,
		];

		if (!isset($map[$type])) {
			abort(404, 'Unknown type');
		}

		return $map[$type];
	}

	/**
	 * check code of symptom
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function checkSymptomCode(Request $request): JsonResponse
	{
		$code = $request->get('code');
		$symptom = MigraineSymptom::where('code', $code)->first();

		return response()->json([
			'exists' => $symptom !== null,
			'item' => $symptom ? [
				'name' => $symptom->getNameAttribute(),
				'code' => $symptom->code
			] : null
		]);
	}

	/**
	 * check code of trigger
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function checkTriggerCode(Request $request): JsonResponse
	{
		$code = $request->get('code');
		$trigger = MigraineTrigger::where('code', $code)->first();

		return response()->json([
			'exists' => $trigger !== null,
			'item' => $trigger ? [
				'name' => $trigger->getNameAttribute(),
				'code' => $trigger->code
			] : null
		]);
	}

	/**
	 * check code of medication
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function checkMedsCode(Request $request): JsonResponse
	{
		$code = $request->get('code');
		$meds = MigraineMed::where('code', $code)->first();

		return response()->json([
			'exists' => $meds !== null,
			'item' => $meds ? [
				'name' => $meds->getNameAttribute(),
				'code' => $meds->code
			] : null
		]);
	}

}
