<?php

namespace Modules\MigraineDiary\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\MigraineDiary\App\Http\Requests\GoogleSheetsRequest;
use Modules\MigraineDiary\App\Http\Requests\SendEmailRequest;
use Modules\MigraineDiary\App\Models\UserOauthToken;
use Modules\MigraineDiary\App\Services\ExcelExportService;
use Modules\MigraineDiary\App\Services\GoogleSheetsService;
use Modules\MigraineDiary\App\Services\MigraineEmailService;
use Modules\MigraineDiary\App\Services\MigraineExportService;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * MigraineDiaryStatisticController
 *
 * @package Modules\MigraineDiary\App\Http\Controllers
 *
 */
class MigraineDiaryStatisticController extends Controller
{
	/**
	 * Download data in .xlsx format
	 *
	 * @param Request $request
	 * @return BinaryFileResponse
	 * @throws Exception
	 */
	public function sheetDownload(Request $request): BinaryFileResponse
	{
		$data = app(MigraineExportService::class)->prepareData(
			auth()->user(),
			$request->input('period', 'month')
		);

		return Excel::download(new ExcelExportService($data), 'migraine-report-'. now()->format('YmHdms') .'.xlsx');
	}

	/**
	 * Download data in PDF format
	 */
	public function pdfDownload()
	{

	}

	/**
	 * Send data to email
	 *
	 * @param SendEmailRequest $request
	 * @return JsonResponse
	 */
	public function sendToEmail(SendEmailRequest $request): JsonResponse
	{
		try {
			$validated = $request->validated();

			app(MigraineEmailService::class)->sendReport(auth()->user(), $validated);

			return response()->json([
				'success' => true,
				'message' => trans('migrainediary::migraine_diary.email_sent_success')
			]);

		} catch (\Exception $e) {
			\Log::error('Email send error: ' . $e->getMessage(), [
				'user_id' => auth()->id(),
				'request' => $request->all()
			]);

			return response()->json([
				'success' => false,
				'message' => trans('migrainediary::migraine_diary.email_sent_error')
			], 500);
		}
	}

	/**
	 * Send data to Google Sheets
	 *
	 * @param GoogleSheetsRequest $request
	 * @return JsonResponse
	 */
	public function sendToGoogleSheets(GoogleSheetsRequest $request): JsonResponse
	{
		$user = auth()->user();

		/** @var UserOauthToken|null $token */
		$token = $user->oauthTokens()->where('provider', 'google')->first();

		if (!$token) {
			return response()->json([
				'success' => false,
				'message' => 'Google аккаунт не подключён.',
			], 400);
		}

		$accessToken = $token->getValidAccessToken();

		if (!$accessToken) {
			return response()->json([
				'success' => false,
				'message' => 'Не удалось получить действительный токен доступа.',
			], 401);
		}

		$data = app(MigraineExportService::class)->prepareData(
			$user,
			$request->input('period', 'month')
		);

		try {
			app(GoogleSheetsService::class)->uploadData($accessToken, $data, $token->metadata);
			return response()->json([
				'success' => true,
				'message' => 'Данные успешно отправлены в Google Sheets',
			]);
		} catch (\Throwable $e) {
			\Log::error('Google Sheets upload failed', [
				'user_id' => $user->id,
				'error' => $e->getMessage(),
			]);
			return response()->json([
				'success' => false,
				'message' => 'Ошибка при отправке данных в Google Sheets',
			], 500);
		}
	}

}
