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
     * @param  string  $type  symptoms | meds | triggers
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
     * @param  string  $type  symptoms | meds | triggers
     * @param  int  $id  selected item
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
     * @param  StoreEntityRequest  $request  validator
     * @param  string  $type  symptoms | meds | triggers
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
     * @param  string  $type  symptoms | meds | triggers
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
     * @param  string  $type  symptoms | meds | triggers
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
