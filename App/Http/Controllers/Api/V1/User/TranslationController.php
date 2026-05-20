<?php

namespace Modules\MigraineDiary\App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Returns localized UI labels for the Migraine Diary frontend.
 */
class TranslationController extends Controller
{
	/**
	 * Resolve the requested locale and return module translation strings.
	 *
	 * @param Request $request HTTP request with optional Accept-Language header.
	 * @return JsonResponse JSON response containing translations and locale metadata.
	 */
	public function __invoke(Request $request): JsonResponse
	{
		$locale = substr($request->header('Accept-Language', app()->getLocale()), 0, 2);

		if (!in_array($locale, ['en', 'de', 'ru'], true)) {
			$locale = 'en';
		}

		return response()->json([
			'data' => trans('migrainediary::migraine_diary', [], $locale),
			'meta' => [
				'locale' => $locale,
			],
		]);
	}
}
