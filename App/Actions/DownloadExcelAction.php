<?php

namespace Modules\MigraineDiary\App\Actions;

use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Modules\MigraineDiary\App\Data\ReportExportData;
use Modules\MigraineDiary\App\Services\ExcelExportService;
use Modules\MigraineDiary\App\Services\MigraineExportService;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Builds and returns an Excel download for the user's migraine report.
 */
class DownloadExcelAction
{
	/**
	 * @param MigraineExportService $export Service preparing report rows.
	 */
	public function __construct(
		private readonly MigraineExportService $export,
	) {}

	/**
	 * Build an Excel download response for the requested report period.
	 *
	 * @param User $user Report owner.
	 * @param ReportExportData $data Export options.
	 * @return BinaryFileResponse
	 * @throws Exception
	 */
	public function execute(User $user, ReportExportData $data): BinaryFileResponse
	{
		$rows = $this->export->prepareData($user, $data->period);

		return Excel::download(
			new ExcelExportService($rows),
			'migraine-report-' . now()->format('Y-m-d_His') . '.xlsx',
		);
	}
}
