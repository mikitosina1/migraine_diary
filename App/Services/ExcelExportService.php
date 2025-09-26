<?php

namespace Modules\MigraineDiary\App\Services;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelExportService implements FromArray, WithHeadings, WithStyles
{
	public function __construct(private readonly array $data) {}

	public function array(): array
	{
		return $this->data;
	}

	public function headings(): array
	{
		return ['Date', 'Pain Level', 'Duration', 'Symptoms', 'Triggers', 'Medications', 'Notes'];
	}

	public function styles(Worksheet $sheet): array
	{
		$sheet->getStyle('A1:G1')->getFont()->setBold(true);

		foreach(range('A','G') as $column) {
			$sheet->getColumnDimension($column)->setAutoSize(true);
		}

		return [
			'B2:B100' => [
				'font' => ['color' => ['rgb' => 'FF0000']]
			],
		];
	}
}
