<?php

namespace Modules\MigraineDiary\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Modules\MigraineDiary\App\Models\{Med, Symptom, Trigger};
use Modules\MigraineDiary\App\Http\Requests\Admin\StoreEntityRequest;
use Modules\MigraineDiary\App\Http\Requests\Admin\UpdateEntityRequest;
use Modules\MigraineDiary\App\Services\Admin\EntityService;

/**
 * Class MigraineDiaryAdminController
 *
 * @package Modules\MigraineDiary\App\Http\Controllers\Admin
 */
class MigraineDiaryAdminController extends Controller
{
	public function __construct(
		private readonly EntityService $entityService
	) {}

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
		return response()->json($this->entityService->findEntity($type, $id));
	}

	/**
	 * Store a newly created resource in storage.
	 * @param StoreEntityRequest $request
	 * @param string $type type of model
	 * @return JsonResponse
	 */
	public function store(StoreEntityRequest $request, string $type): JsonResponse
	{
		return response()->json(['success' => true, 'item' => $this->entityService->createEntity($type, $request->validated())]);
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
	 * @param UpdateEntityRequest $request
	 * @param string $type type of model
	 * @param int $id id of record
	 * @return JsonResponse
	 */
	public function update(UpdateEntityRequest $request, string $type, int $id): JsonResponse
	{
		return response()->json(['success' => $this->entityService->updateEntity($type, $id, $request->validated())]);
	}

	/**
	 * Remove the specified resource from storage.
	 * @param string $type
	 * @param int $id
	 * @return JsonResponse
	 */
	public function destroy(string $type, int $id): JsonResponse
	{
		return response()->json([
			'success' => true,
			'message' => 'Record ' . $this->entityService->deleteEntity($type, $id) . ' deleted successfully'
		]);
	}
}
