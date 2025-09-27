@vite(['Modules/MigraineDiary/resources/assets/js/admin/diary_admin.js', 'Modules/MigraineDiary/resources/assets/sass/diary_admin.scss'])
<x-app-layout>
	<div class="cloud mx-auto max-w-7xl px-4 py-6">
		<!-- Tab-buttons -->
		<div class="flex border-b dark:border-gray-700 mb-6 tab-buttons">
			<button data-tab="symptoms"
					class="tab-button active px-4 py-2 font-medium">@lang('migrainediary::admin.symptom_list')</button>
			<button data-tab="triggers"
					class="tab-button px-4 py-2 font-medium">@lang('migrainediary::admin.trigger_list')</button>
			<button data-tab="meds"
					class="tab-button px-4 py-2 font-medium">@lang('migrainediary::admin.meds_list')</button>
		</div>

		<!-- Blocks of list (by default - symptoms) -->
		<div id="symptoms-tab" class="tab-content">
			@include('migrainediary::admin._dictionary', [
				'items' => $symptomList,
				'type' => 'symptoms',
				'locales' => $locales
			])
		</div>

		<div id="triggers-tab" class="tab-content hidden">
			@include('migrainediary::admin._dictionary', [
				'items' => $triggerList,
				'type' => 'triggers',
				'locales' => $locales
			])
		</div>

		<div id="meds-tab" class="tab-content hidden">
			@include('migrainediary::admin._dictionary', [
				'items' => $medsList,
				'type' => 'meds',
				'locales' => $locales
			])
		</div>
	</div>

	<!-- modal window for edit -->
	@include('migrainediary::admin._edit_modal')
</x-app-layout>
