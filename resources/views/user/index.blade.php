@vite(['Modules/MigraineDiary/resources/assets/js/diary_main.js', 'Modules/MigraineDiary/resources/assets/sass/diary_main.scss'])
<x-app-layout>
	<div class="cloud mx-auto max-w-7xl px-4 py-6">
		<!-- Calendar Section -->
		<div class="mb-8 calendar-container">
			<!-- Calendar buttons -->
			<div class="flex justify-between items-center mb-4">
				<!-- navigation buttons -->
				<div class="flex space-x-2">
					<button id="prev-month" class="px-3 py-1 border rounded-md">
						<i class="fas fa-chevron-left"></i>
					</button>
					<button id="next-month" class="px-3 py-1 border rounded-md">
						<i class="fas fa-chevron-right"></i>
					</button>
					<button id="today-btn" class="px-3 py-1 border rounded-md">
						@lang('migrainediary::migraine_diary.today')
					</button>
				</div>

				<!-- add button -->
				<button
					onclick="window.migraineModal.showModal()"
					class="px-4 py-2 mb-4 bg-blue-600 text-white rounded-md hover:bg-blue-700"
				>
					@lang('migrainediary::migraine_diary.add_attack')
				</button>
			</div>
			<x-migrainediary::modal
				id="migraineModal"
				title="@lang('migrainediary::migraine_diary.new_attack')"
			>
				@include('migrainediary::user.attacks._form')
			</x-migrainediary::modal>

			<!-- calendar -->
			<div id="migraine-calendar" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4"></div>
		</div>

		<!-- statistics -->
		<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
			{{--TODO: statistics--}}
		</div>
	</div>
</x-app-layout>
