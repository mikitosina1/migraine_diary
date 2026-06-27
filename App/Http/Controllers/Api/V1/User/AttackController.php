<?php

namespace Modules\MigraineDiary\App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\MigraineDiary\App\Actions\User\CreateAttackAction;
use Modules\MigraineDiary\App\Actions\User\DeleteAttackAction;
use Modules\MigraineDiary\App\Actions\User\EndAttackAction;
use Modules\MigraineDiary\App\Actions\User\UpdateAttackAction;
use Modules\MigraineDiary\App\Http\Requests\AttackFilterRequest;
use Modules\MigraineDiary\App\Http\Requests\StoreAttackRequest;
use Modules\MigraineDiary\App\Http\Requests\UpdateAttackRequest;
use Modules\MigraineDiary\App\Http\Resources\AttackResource;
use Modules\MigraineDiary\App\Models\Attack;
use Modules\MigraineDiary\App\Repositories\AttackRepository;
use Modules\MigraineDiary\App\Services\AttackFilterService;
use Throwable;

/**
 * HTTP API for migraine attacks belonging to the authenticated user (v1).
 *
 * Route-model {@see Attack} instances are re-fetched via the repository to enforce ownership.
 */
class AttackController extends Controller
{
    public function __construct(
        private readonly AttackRepository $attacks,
    ) {}

    /**
     * List all attacks for the current user.
     *
     * @return AnonymousResourceCollection<int, AttackResource>
     */
    public function index(
        AttackFilterRequest $request,
        AttackFilterService $filterService
    ): AnonymousResourceCollection {
        $attacks = $filterService->getFilteredAttacks(
            auth()->id(),
            $request->getRange(),
            $request->getPainLevel()
        );

        return AttackResource::collection($attacks);
    }

    /**
     * Store a newly created attack.
     * @throws Throwable
     */
    public function store(StoreAttackRequest $request, CreateAttackAction $action): AttackResource
    {
        $attack = $action->execute($request->toData(), auth()->id());

        return new AttackResource($attack);
    }

    /**
     * Display the specified attack if it belongs to the current user.
     */
    public function show(Attack $attack): AttackResource
    {
        $attack = $this->attacks->findOrFailForUser($attack->id, auth()->id());

        return new AttackResource($attack);
    }

    /**
     * Update the specified attack.
     */
    public function update(UpdateAttackRequest $request, Attack $attack, UpdateAttackAction $action): AttackResource
    {
        $attack = $this->attacks->findOrFailForUser($attack->id, auth()->id());

        $attack = $action->execute($attack, $request->toData());

        return new AttackResource($attack);
    }

    /**
     * Remove the specified attack.
     */
    public function destroy(Attack $attack, DeleteAttackAction $action): JsonResponse
    {
        $attack = $this->attacks->findOrFailForUser($attack->id, auth()->id());

        $action->execute($attack);

        return response()->json(null, 204);
    }

    /**
     * Mark the attack as ended (sets end time via repository / action pipeline).
     */
    public function end(Attack $attack, EndAttackAction $action): AttackResource
    {
        $attack = $this->attacks->findOrFailForUser($attack->id, auth()->id());

        $attack = $action->execute($attack);

        return new AttackResource($attack);
    }

    /**
     * Return the user's currently active attack, if any (see {@see AttackRepository::getActiveAttackForUser}).
     */
    public function active(): AttackResource
    {
        $attack = $this->attacks->getActiveAttackForUser(auth()->id());

        return new AttackResource($attack);
    }
}
