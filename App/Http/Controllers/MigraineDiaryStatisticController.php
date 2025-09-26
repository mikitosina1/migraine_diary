<?php

namespace Modules\MigraineDiary\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Modules\MigraineDiary\App\Services\ExcelExportService;
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

		return Excel::download(new ExcelExportService($data), 'migraine-report.xlsx');
	}

	/**
	 * Download data in PDF format
	 */
	public function pdfDownload()
	{

	}

	/**
	 * Send data to email
	 */
	public function sendToEmail()
	{

	}

	/**
	 * Send data to Google Sheets
	 */
	public function sendToGoogleSheets()
	{

	}
}
