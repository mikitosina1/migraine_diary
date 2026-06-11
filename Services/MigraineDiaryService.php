<?php

namespace Modules\MigraineDiary\Services;

use Nwidart\Modules\Facades\Module;

class MigraineDiaryService
{
    /**
     * Get the path to a module's view.
     */
    public function getViewPath(string $viewPath): ?string
    {
        if ($this->isModuleActive()) {
            return view()->exists("migrainediary::{$viewPath}") ? "migrainediary::{$viewPath}" : null;
        }

        return null;
    }

    /**
     * Check if the SupportChat module is active.
     */
    public function isModuleActive(): bool
    {
        $enabledModules = Module::allEnabled();

        return isset($enabledModules['MigraineDiary']);
    }
}
