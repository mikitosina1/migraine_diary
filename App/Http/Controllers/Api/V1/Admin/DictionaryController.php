<?php

namespace Modules\MigraineDiary\App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\MigraineDiary\App\Actions\Admin\CreateDictionaryEntityAction;
use Modules\MigraineDiary\App\Actions\Admin\DeleteDictionaryEntityAction;
use Modules\MigraineDiary\App\Actions\Admin\ListAllDictionaryEntitiesAction;
use Modules\MigraineDiary\App\Actions\Admin\ListDictionaryEntitiesAction;
use Modules\MigraineDiary\App\Actions\Admin\PatchDictionaryEntityAction;
use Modules\MigraineDiary\App\Actions\Admin\ShowDictionaryEntityAction;
use Modules\MigraineDiary\App\Http\Requests\Admin\PatchEntityRequest;
use Modules\MigraineDiary\App\Http\Requests\Admin\StoreEntityRequest;
use Modules\MigraineDiary\App\Http\Resources\Admin\DictionaryEntityResource;
use Modules\MigraineDiary\App\Http\Resources\Admin\DictionaryResource;

/**
 * HTTP API for standard dictionaries (v1).
 */
class DictionaryController extends Controller
{
    /**
     * List all dictionaries
     * @param ListAllDictionaryEntitiesAction $action
     *
     * @return DictionaryResource
     */
    public function indexAll(
        ListAllDictionaryEntitiesAction $action
    ): DictionaryResource {
        return new DictionaryResource(
            $action->execute()
        );
    }

    /**
     * List selected category dictionary
     *
     * @param ListDictionaryEntitiesAction $action
     * @param string $type symptoms | meds | triggers
     *
     * @return AnonymousResourceCollection
     */
    public function index(
        ListDictionaryEntitiesAction $action,
        string $type
    ): AnonymousResourceCollection {
        return DictionaryEntityResource::collection(
            $action->execute($type)
        );
    }

    /**
     * Shows selected entity
     *
     * @param ShowDictionaryEntityAction $action
     * @param string $type symptoms | meds | triggers
     * @param int $id selected item
     *
     * @return DictionaryEntityResource
     */
    public function show(
        ShowDictionaryEntityAction $action,
        string $type,
        int $id
    ): DictionaryEntityResource {
        return new DictionaryEntityResource(
            $action->execute($type, $id)
        );
    }

    /**
     * @param StoreEntityRequest $request validator
     * @param CreateDictionaryEntityAction $action
     * @param string $type symptoms | meds | triggers
     *
     * @return DictionaryEntityResource
     */
    public function store(
        StoreEntityRequest $request,
        CreateDictionaryEntityAction $action,
        string $type
    ): DictionaryEntityResource {
        return new DictionaryEntityResource(
            $action->execute($type, $request->validated())
        );
    }

    /**
     * @param PatchEntityRequest $request
     * @param PatchDictionaryEntityAction $action
     * @param string $type symptoms | meds | triggers
     * @param int $id
     *
     * @return DictionaryEntityResource
     */
    public function patch(
        PatchEntityRequest $request,
        PatchDictionaryEntityAction $action,
        string $type,
        int $id
    ): DictionaryEntityResource {
        return new DictionaryEntityResource(
            $action->execute($type, $id, $request->validated())
        );
    }

    /**
     * @param DeleteDictionaryEntityAction $action
     * @param string $type symptoms | meds | triggers
     * @param int $id
     *
     * @return JsonResponse
     */
    public function destroy(
        DeleteDictionaryEntityAction $action,
        string $type,
        int $id
    ): JsonResponse {
        $action->execute($type, $id);

        return response()->json(null, 204);
    }
}
