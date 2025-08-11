@php
	use Nwidart\Modules\Module;

	$assets = Module::getAssets();
	$moduleAssets = array_filter($assets, function ($asset) {
		return str_contains($asset, 'Modules/MigraineDiary');
	});
@endphp

@foreach ($moduleAssets as $asset)
	@vite($asset)
@endforeach
<div class="dashboard-widget migraine-diary cloud-widget-item">
	<h3>@lang('migrainediary::migraine_diary.widget_title')</h3>
</div>
