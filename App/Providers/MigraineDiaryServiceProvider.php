<?php

namespace Modules\MigraineDiary\App\Providers;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\MigraineDiary\Services\MigraineDiaryService;
use Modules\ModuleManager\App\Services\ModuleAdminActionRegistrar;

class MigraineDiaryServiceProvider extends ServiceProvider
{
	protected string $moduleName = 'MigraineDiary';

	protected string $moduleNameLower = 'migrainediary';

	/**
	 * Boot the application events.
	 * @throws BindingResolutionException
	 */
	public function boot(): void
	{
		$this->registerCommands();
		$this->registerCommandSchedules();
		$this->registerTranslations();
		$this->registerConfig();
		$this->registerViews();
		$this->loadMigrationsFrom(module_path($this->moduleName, 'Database/migrations'));
		$this->loadViewsFrom(__DIR__ . '/../../resources/views', $this->moduleNameLower);

		ModuleAdminActionRegistrar::register(
			$this->moduleName,
			$this->moduleNameLower,
			'admin.migraine-diary.index',
			$this->moduleNameLower . '::admin.config_diary',
			'📔',
			fn() => $this->app->make(MigraineDiaryService::class)->isModuleActive()
		);
	}

	/**
	 * Register commands in the format of Command::class
	 */
	protected function registerCommands(): void
	{
		// $this->commands([]);
	}

	/**
	 * Register command Schedules.
	 */
	protected function registerCommandSchedules(): void
	{
		// $this->app->booted(function () {
		//     $schedule = $this->app->make(Schedule::class);
		//     $schedule->command('inspire')->hourly();
		// });
	}

	/**
	 * Register translations.
	 */
	public function registerTranslations(): void
	{
		$langPath = resource_path('lang/' . $this->moduleNameLower);

		if (is_dir($langPath)) {
			$this->loadTranslationsFrom($langPath, $this->moduleNameLower);
			$this->loadJsonTranslationsFrom($langPath);
		} else {
			$this->loadTranslationsFrom(module_path($this->moduleName, 'resources/lang'), $this->moduleNameLower);
			$this->loadJsonTranslationsFrom(module_path($this->moduleName, 'resources/lang'));
		}
	}

	/**
	 * Register config.
	 */
	protected function registerConfig(): void
	{
		$this->publishes([module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower . '.php')], 'config');
		$this->mergeConfigFrom(module_path($this->moduleName, 'config/config.php'), $this->moduleNameLower);
	}

	/**
	 * Register views.
	 */
	public function registerViews(): void
	{
		$viewPath = resource_path('views/modules/' . $this->moduleNameLower);
		$sourcePath = module_path($this->moduleName, 'resources/views');

		$this->publishes([$sourcePath => $viewPath], ['views', $this->moduleNameLower . '-module-views']);

		$this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);

		$componentNamespace = str_replace('/', '\\', config('modules.namespace') . '\\' . $this->moduleName . '\\' . config('modules.paths.generator.component-class.path'));
		Blade::componentNamespace($componentNamespace, $this->moduleNameLower);
	}

	private function getPublishableViewPaths(): array
	{
		$paths = [];
		foreach (config('view.paths') as $path) {
			if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
				$paths[] = $path . '/modules/' . $this->moduleNameLower;
			}
		}

		return $paths;
	}

	/**
	 * Register the service provider.
	 */
	public function register(): void
	{
		$this->app->register(RouteServiceProvider::class);
	}

	/**
	 * Get the services provided by the provider.
	 */
	public function provides(): array
	{
		return [];
	}
}
