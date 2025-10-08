@php
	use Carbon\Carbon;
	$currentRange = $currentRange ?? 'month';
@endphp
	<div class="statistic-header flex flex-row justify-between items-center p-4">
		<div class="filter-block mb-4">
			<select id="statistic-attack-range" class="bg-gray-800 text-white py-2 rounded">
				<option value="month" {{ $currentRange === 'month' ? 'selected' : '' }}>
					@lang('migrainediary::migraine_diary.last_month')
				</option>
				<option value="3months" {{ $currentRange === '3months' ? 'selected' : '' }}>
					@lang('migrainediary::migraine_diary.last_3_months')
				</option>
				<option value="year" {{ $currentRange === 'year' ? 'selected' : '' }}>
					@lang('migrainediary::migraine_diary.last_year')
				</option>
			</select>
		</div>
	</div>
@if($attacks)
	<div class="statistic-send flex flex-col">
		<!-- XML Download -->
		<form action="{{ route('user.migraine-diary.download-sheet') }}" method="POST" class="xml-download-form
				text-white m-2 text-white dark:bg-gray-900">
			@csrf
			<input type="hidden" name="period" value="{{ $currentRange }}">
			<button type="submit">
				@lang('migrainediary::migraine_diary.generate_sheet')
			</button>
		</form>
		<!-- Send Email -->
		<div class="bordered-block-toggler to-email m-2 text-white dark:bg-gray-900">
			@lang('migrainediary::migraine_diary.send_to_email')
		</div>
		<!-- Send Email -->
		<div class="bordered-block to-email-target flex-col justify-between m-2 p-1 text-white hidden">
			<div class="form-group mb-4">
				<label class="flex items-center space-x-2">
					<input type="radio" name="recipient_type" value="self" checked class="radio recipient-radio">
					<span>@lang('migrainediary::migraine_diary.to_your_email')</span>
				</label>
				<label class="flex items-center space-x-2 mt-2">
					<input type="radio" name="recipient_type" value="doctor" class="radio recipient-radio">
					<span>@lang('migrainediary::migraine_diary.to_docs_email')</span>
				</label>
			</div>

			<!-- Email input (for doctor) -->
			<div class="form-group doctor-email-field hidden">
				<label class="block text-sm font-medium mb-1">
					@lang('migrainediary::migraine_diary.doctor_email')
				</label>
				<input
					type="email"
					name="doctor_email"
					placeholder="doctor@example.com"
					class="w-full p-2 border rounded text-gray-800"
					disabled
				>
			</div>

			<button type="button" class="send-email-btn mt-2 p-1">
				@lang('migrainediary::migraine_diary.send')
			</button>
		</div>

		<!-- Google Sheets -->
		<div class="bordered-block-toggler google-sheets m-2 text-white dark:bg-gray-900">
			@lang('migrainediary::migraine_diary.add_to_google_sheets')
		</div>
		<div class="bordered-block google-sheets-target flex-row justify-between items-center m-2 p-1 text-white hidden">

		</div>
	</div>
	<div class="statistic">

	</div>
@else
	@lang('migrainediary::migraine_diary.no_rec_found')
@endif
