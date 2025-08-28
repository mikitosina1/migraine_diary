<form method="POST" id="migraine-form"
	  action="{{ $mode === 'edit' ? route('user.migraine-diary.attacks.update', $attack) :
	  route('user.migraine-diary.resource.store') }}"
>
	@csrf
	@if($mode === 'edit')
		@method('PUT')
	@endif
	<!-- Progress bar -->
	@include('migrainediary::components.steps', [
			'form_steps' => [
			'details' => __('migrainediary::migraine_diary.details'),
			'symptoms' => __('migrainediary::migraine_diary.symptoms'),
			'triggers' => __('migrainediary::migraine_diary.triggers'),
			'meds' => __('migrainediary::migraine_diary.meds'),
			'review' => __('migrainediary::migraine_diary.review')
		]
	])

	<!-- Step 1: Main details -->
	<div class="step" data-step="1">
		@include('migrainediary::user.attacks._step_details')
	</div>

	<!-- Step 2: Symptoms -->
	<div class="step hidden" data-step="2">
		@include('migrainediary::user.attacks._step_symptoms')
	</div>

	<!-- Step 3: Triggers -->
	<div class="step hidden" data-step="3">
		@include('migrainediary::user.attacks._step_triggers')
	</div>

	<!-- Step 4: Meds -->
	<div class="step hidden" data-step="4">
		@include('migrainediary::user.attacks._step_meds')
	</div>

	<!-- Step 5: Summary -->
	<div class="step hidden" data-step="5">
		@include('migrainediary::user.attacks._summary')
	</div>

	<!-- Navigation buttons -->
	<div class="flex justify-between mt-8">
		<button type="button"
				title="@lang('migrainediary::migraine_diary.back')"
				class="prev-btn px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300
				dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 transition"
		>
			@lang('migrainediary::migraine_diary.back')
		</button>
		<button type="button"
				title="@lang('migrainediary::migraine_diary.next')"
				class="next-btn px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white shadow-md transition"
		>
			@lang('migrainediary::migraine_diary.next')
		</button>
	</div>

</form>
