<?php

namespace Modules\MigraineDiary\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\MigraineDiary\App\Models\Med;
use Modules\MigraineDiary\App\Models\Symptom;
use Modules\MigraineDiary\App\Models\Trigger;

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
			'symptomList' => Symptom::getListWithTranslations(),
			'triggerList' => Trigger::getListWithTranslations(),
			'medsList' => Med::getListWithTranslations(),
			'locales' => config('app.locales')
		]);
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
		/** @var $modelClass Symptom|Trigger|Med */
		$modelClass = $this->getModelByType($type);

		$model = $modelClass::with('translations')->findOrFail($id);

		return response()->json($model);
	}

	/**
	 * get model class by type
	 * @param string $type
	 * @return string
	 */
	private function getModelByType(string $type): string
	{
		$map = [
			'symptoms' => Symptom::class,
			'triggers' => Trigger::class,
			'meds'     => Med::class,
		];

		if (!isset($map[$type])) {
			abort(404, 'Unknown type');
		}

		return $map[$type];
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

		/** @var $modelClass Symptom|Trigger|Med */
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
	 * Show the form for creating a new resource.
	 * @return View|Factory|Application
	 */
	public function create(): View|Factory|Application
	{
		return view('migrainediary::create');
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

		/** @var $modelClass Symptom|Trigger|Med */
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
		/** @var $model Symptom|Trigger|Med */
		$model = $this->getModelByType($type);
		$item = $model::findOrFail($id);
		$code = $item->code;
		$item->translations()->delete();
		$item->delete();

		return response()->json(['success' => true, 'message' => 'Record ' . $code . ' deleted successfully']);
	}

	/**
	 * check code of symptom
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function checkSymptomCode(Request $request): JsonResponse
	{
		$code = $request->get('code');
		$symptom = Symptom::where('code', $code)->first();

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
		$trigger = Trigger::where('code', $code)->first();

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
		$meds = Med::where('code', $code)->first();

		return response()->json([
			'exists' => $meds !== null,
			'item' => $meds ? [
				'name' => $meds->getNameAttribute(),
				'code' => $meds->code
			] : null
		]);
	}

}
