<?php

namespace Modules\MigraineDiary\App\Data;

/**
 * DTO for migraine report export (Excel / PDF).
 */
class ReportExportData
{
	/**
	 * @param string $period Supported period key: month, 3months, or year.
	 */
	public function __construct(
		public readonly string $period,
	) {}

	/**
	 * Create export DTO from a validated period/range value.
	 *
	 * @param string $range Supported period key: month, 3months, or year.
	 * @return self
	 */
	public static function fromRange(string $range): self
	{
		return new self(period: $range);
	}
}
