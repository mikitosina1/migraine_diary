<?php

namespace Modules\MigraineDiary\App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\MigraineDiary\App\Actions\StatisticAction;
use Modules\MigraineDiary\App\Http\Requests\AttackFilterRequest;

class StatisticController extends Controller
{
	public function __invoke(
		AttackFilterRequest $request,
		StatisticAction $action
	): JsonResponse
	{
		return response()->json([
			'data' => $action->execute(
				auth()->user()->id,
				$request->getRange(),
				$request->getPainLevel()
			),
		]);
	}
}
