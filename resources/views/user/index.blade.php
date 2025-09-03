@vite(['Modules/MigraineDiary/resources/assets/js/diary_main.js', 'Modules/MigraineDiary/resources/assets/sass/diary_main.scss'])
<x-app-layout>
	<div class="cloud migraine-diary mt-8 mx-auto max-w-7xl dark:bg-gray-900 sm:rounded-lg">
		<!-- Calendar Section -->
		<div class="calendar-container p-4">
			<!-- Calendar buttons -->
			<div class="flex justify-between items-center mb-4 calendar-buttons">
				<div class="tabs flex space-x-4">
					<button class="tab-btn px-4 py-2 text-white" data-tab="calendar">
						@lang('migrainediary::migraine_diary.calendar')
					</button>
					<button class="tab-btn px-4 py-2 text-white active" data-tab="list">
						@lang('migrainediary::migraine_diary.list')
					</button>
					<button class="tab-btn px-4 py-2 text-white" data-tab="statistic">
						@lang('migrainediary::migraine_diary.statistic')
					</button>
				</div>
				<!-- add button -->
				<button
					data-action="add-attack"
					class="px-3 py-1 ml-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-700"
				>
					@lang('migrainediary::migraine_diary.add_attack')
				</button>
			</div>
		</div>
		<!-- calendar -->
		<div id="tab-calendar" class="tab-content hidden">
			<livewire:migrainediary.migraine-calendar />
		</div>

		<!-- list by default -->
		<div id="tab-list" class="tab-content">
			<x-migrainediary::attacks-list :attacks="$attacks"/>
		</div>

		<!-- statistics -->
		<div id="tab-statistic" class="tab-content hidden">
			<x-migrainediary::attacks-statistics :attacks="$attacks"/>
		</div>

		<!-- modal -->
		<x-migrainediary::modal>
			@include('migrainediary::user.attacks._form', ['mode' => 'create'])
		</x-migrainediary::modal>

	</div>
</x-app-layout>
