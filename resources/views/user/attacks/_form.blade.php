<form method="POST" id="migraine-form" action="{{ route('user.migraine-diary.resource.store') }}">
	@csrf
	<!-- Progress bar -->
	@include('migrainediary::components.steps', [
			'form_steps' => [
			'details' => '1. '.__('migrainediary::migraine_diary.details'),
			'symptoms' => '2. '.__('migrainediary::migraine_diary.symptoms'),
			'meds' => '3. '.__('migrainediary::migraine_diary.meds'),
			'review' => '4. '.__('migrainediary::migraine_diary.review')
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

	<!-- Step 3: Meds -->
	<div class="step hidden" data-step="3">
		@include('migrainediary::user.attacks._step_meds')
	</div>

	<!-- Step 4: Summary -->
	<div class="step hidden" data-step="4">
		@include('migrainediary::user.attacks._summary')
	</div>

	<!-- Navigation buttons -->
	<div class="flex justify-between mt-6">
		<button type="button" class="prev-btn bg-blue-600 rounded-md hover:bg-blue-700 text-white p-1">@lang('migrainediary::migraine_diary.back')</button>
		<button type="button" class="next-btn bg-blue-600 rounded-md hover:bg-blue-700 text-white p-1">@lang('migrainediary::migraine_diary.next')</button>
	</div>
</form>
