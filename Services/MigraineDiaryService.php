<?php

namespace Modules\MigraineDiary\Services;

use Nwidart\Modules\Facades\Module;

class MigraineDiaryService
{
	/**
	 * Check if the SupportChat module is active.
	 *
	 * @return bool
	 */
	public function isModuleActive(): bool
	{
		$enabledModules = Module::allEnabled();

		return isset($enabledModules['MigraineDiary']);
	}

	/**
	 * Get the path to a module's view.
	 *
	 * @param string $viewPath
	 * @return string|null
	 */
	public function getViewPath(string $viewPath): ?string
	{
		if ($this->isModuleActive()) {
			return view()->exists("migrainediary::{$viewPath}") ? "migrainediary::{$viewPath}" : null;
		}
		return null;
	}
}
