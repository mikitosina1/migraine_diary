<?php

namespace Modules\MigraineDiary\App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\MigraineDiary\App\Actions\DashboardDataAction;
use Modules\MigraineDiary\App\Http\Resources\DashboardResource;

/**
 * HTTP API endpoint that returns aggregated dashboard data for the authenticated user (v1).
 */
class DashboardController extends Controller
{
	/**
	 * Build the dashboard payload (active attack, recent list, dictionaries, statistics).
	 *
	 * @param Request $request
	 * @param DashboardDataAction $action
	 * @return DashboardResource
	 */
	public function __invoke(
		Request $request,
		DashboardDataAction $action
	): DashboardResource
	{
		$dashboardData = $action->execute($request->user()->id);

		return new DashboardResource($dashboardData);
	}
}
