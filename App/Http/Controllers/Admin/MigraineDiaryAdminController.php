<?php

namespace Modules\MigraineDiary\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Modules\MigraineDiary\App\Http\Requests\Admin\StoreEntityRequest;
use Modules\MigraineDiary\App\Http\Requests\Admin\UpdateEntityRequest;
use Modules\MigraineDiary\App\Models\Med;
use Modules\MigraineDiary\App\Models\Symptom;
use Modules\MigraineDiary\App\Models\Trigger;
use Modules\MigraineDiary\App\Services\Admin\EntityService;

/**
 * Class MigraineDiaryAdminController
 */
class MigraineDiaryAdminController extends Controller
{
    public function __construct(
        private readonly EntityService $entityService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        return view('migrainediary::admin.index-admin', [
            'symptomList' => Symptom::getListWithTranslations(),
            'triggerList' => Trigger::getListWithTranslations(),
            'medsList' => Med::getListWithTranslations(),
            'locales' => config('app.locales'),
        ]);
    }

    /**
     * Show the specified resource.
     */
    public function show(int $id): View|Factory|Application
    {
        return view('migrainediary::show');
    }

    /**
     * Show the data for editing the specified resource.
     *
     * @param  string  $type  type of model
     * @param  int  $id  id of record
     */
    public function edit(string $type, int $id): JsonResponse
    {
        return response()->json($this->entityService->findEntity($type, $id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  string  $type  type of model
     */
    public function store(StoreEntityRequest $request, string $type): JsonResponse
    {
        return response()->json(['success' => true, 'item' => $this->entityService->createEntity($type, $request->validated())]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        return view('migrainediary::create');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  string  $type  type of model
     * @param  int  $id  id of record
     */
    public function update(UpdateEntityRequest $request, string $type, int $id): JsonResponse
    {
        return response()->json(['success' => $this->entityService->updateEntity($type, $id, $request->validated())]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $type, int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Record '.$this->entityService->deleteEntity($type, $id).' deleted successfully',
        ]);
    }
}
