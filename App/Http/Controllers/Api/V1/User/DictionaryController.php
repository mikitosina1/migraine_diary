<?php

namespace Modules\MigraineDiary\App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\MigraineDiary\App\Http\Resources\DictionaryResource;
use Modules\MigraineDiary\App\Services\DictionaryService;

/**
 * HTTP API endpoint that returns catalog and user-specific dictionary entries (v1).
 */
class DictionaryController extends Controller
{
	/**
	 * Return symptoms, triggers, medications (catalog + user-defined) for the authenticated user.
	 *
	 * @param Request $request
	 * @param DictionaryService $dictionaryService
	 * @return DictionaryResource
	 */
	public function __invoke(Request $request, DictionaryService $dictionaryService): DictionaryResource
	{
		$dictionaries = $dictionaryService->getForUser(auth()->user()->id);

		return new DictionaryResource($dictionaries);
	}
}
