<?php

namespace Modules\MigraineDiary\App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Modules\MigraineDiary\App\Actions\DownloadExcelAction;
use Modules\MigraineDiary\App\Actions\DownloadPdfAction;
use Modules\MigraineDiary\App\Actions\SendEmailAction;
use Modules\MigraineDiary\App\Http\Requests\ReportExportRequest;
use Modules\MigraineDiary\App\Http\Requests\SendEmailRequest;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * HTTP API for migraine report export (email, Excel, PDF) for the authenticated user (v1).
 */
class ReportController extends Controller
{
	/**
	 * Send a migraine report to the authenticated user or doctor.
	 *
	 * @param SendEmailRequest $request
	 * @param SendEmailAction $action
	 * @return JsonResponse
	 */
	public function sendEmail(
		SendEmailRequest $request,
		SendEmailAction $action,
	): JsonResponse {
		$action->execute($request->user(), $request->toData());

		return response()->json([
			'data' => [
				'message' => trans('migrainediary::migraine_diary.email_sent_success'),
			],
		]);
	}

	/**
	 * Download the authenticated user's migraine report as an Excel file.
	 *
	 * @param ReportExportRequest $request
	 * @param DownloadExcelAction $action
	 * @return BinaryFileResponse
	 * @throws Exception
	 */
	public function downloadExcel(
		ReportExportRequest $request,
		DownloadExcelAction $action,
	): BinaryFileResponse {
		return $action->execute($request->user(), $request->toData());
	}

	/**
	 * Download the authenticated user's migraine report as a PDF file.
	 *
	 * @param ReportExportRequest $request
	 * @param DownloadPdfAction $action
	 * @return Response
	 */
	public function downloadPdf(
		ReportExportRequest $request,
		DownloadPdfAction $action,
	): Response {
		return $action->execute($request->user(), $request->toData());
	}
}
