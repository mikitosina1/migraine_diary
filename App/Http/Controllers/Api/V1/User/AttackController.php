<?php

namespace Modules\MigraineDiary\App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\MigraineDiary\App\Http\Resources\AttackResource;
use Modules\MigraineDiary\App\Actions\CreateAttackAction;
use Modules\MigraineDiary\App\Actions\DeleteAttackAction;
use Modules\MigraineDiary\App\Actions\EndAttackAction;
use Modules\MigraineDiary\App\Models\Attack;
use Modules\MigraineDiary\App\Repositories\AttackRepository;
use Modules\MigraineDiary\App\Http\Requests\StoreAttackRequest;
use Modules\MigraineDiary\App\Actions\UpdateAttackAction;
use Modules\MigraineDiary\App\Http\Requests\UpdateAttackRequest;

/**
 * HTTP API for migraine attacks belonging to the authenticated user (v1).
 *
 * Route-model {@see Attack} instances are re-fetched via the repository to enforce ownership.
 */
class AttackController extends Controller
{
	/**
	 * @param AttackRepository $attacks
	 */
	public function __construct(
		private readonly AttackRepository $attacks,
	) {}

	/**
	 * List all attacks for the current user.
	 *
	 * @return AnonymousResourceCollection<int, AttackResource>
	 */
	public function index(): AnonymousResourceCollection
	{
		return AttackResource::collection(
			$this->attacks->getUserAttacks(auth()->id())
		);
	}

	/**
	 * Store a newly created attack.
	 *
	 * @param StoreAttackRequest $request
	 * @param CreateAttackAction $action
	 * @return AttackResource
	 */
	public function store(StoreAttackRequest $request, CreateAttackAction $action): AttackResource
	{
		$attack = $action->execute($request->toData(), auth()->id());

		return new AttackResource($attack);
	}

	/**
	 * Display the specified attack if it belongs to the current user.
	 *
	 * @param Attack $attack
	 * @return AttackResource
	 */
	public function show(Attack $attack): AttackResource
	{
		$attack = $this->attacks->findOrFailForUser($attack->id, auth()->id());

		return new AttackResource($attack);
	}

	/**
	 * Update the specified attack.
	 *
	 * @param UpdateAttackRequest $request
	 * @param Attack $attack
	 * @param UpdateAttackAction $action
	 * @return AttackResource
	 */
	public function update(UpdateAttackRequest $request, Attack $attack, UpdateAttackAction $action): AttackResource
	{
		$attack = $this->attacks->findOrFailForUser($attack->id, auth()->id());

		$attack = $action->execute($attack, $request->toData());

		return new AttackResource($attack);
	}

	/**
	 * Remove the specified attack.
	 *
	 * @param Attack $attack
	 * @param DeleteAttackAction $action
	 * @return JsonResponse
	 */
	public function destroy(Attack $attack, DeleteAttackAction $action): JsonResponse
	{
		$attack = $this->attacks->findOrFailForUser($attack->id, auth()->id());

		$action->execute($attack);

		return response()->json(null, 204);
	}

	/**
	 * Mark the attack as ended (sets end time via repository / action pipeline).
	 *
	 * @param Attack $attack
	 * @param EndAttackAction $action
	 * @return AttackResource
	 */
	public function end(Attack $attack, EndAttackAction $action): AttackResource
	{
		$attack = $this->attacks->findOrFailForUser($attack->id, auth()->id());

		$attack = $action->execute($attack);

		return new AttackResource($attack);
	}

	/**
	 * Return the user's currently active attack, if any (see {@see AttackRepository::getActiveAttackForUser}).
	 *
	 * @return AttackResource
	 */
	public function active(): AttackResource
	{
		$attack = $this->attacks->getActiveAttackForUser(auth()->id());

		return new AttackResource($attack);
	}
}
