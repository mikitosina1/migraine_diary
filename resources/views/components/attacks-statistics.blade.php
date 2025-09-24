@php
	use Carbon\Carbon;
	$currentRange = $currentRange ?? 'month';
@endphp
@if($attacks)
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
	<div class="statistic flex flex-row justify-between items-center">
		<div class="controls share-content flex flex-row justify-between items-center m-2">
			<!-- XML Download -->
			<form action="{{ route('user.migraine-diary.download-sheet') }}" method="POST" class="mr-2
				text-white rounded bg-blue-500 hover:bg-blue-700 p-1 mb-0">
				@csrf
				<button type="submit">
					@lang('migrainediary::migraine_diary.generate_sheet')
				</button>
			</form>
			<!-- Send Email -->
			<button class="send-to-email text-white rounded bg-blue-500 hover:bg-blue-700 mr-2 p-1">
				@lang('migrainediary::migraine_diary.send_to_email')
			</button>
			<!-- Google Sheets -->
			<button class="add-to-google-sheets text-white rounded bg-blue-500 hover:bg-blue-700 p-1">
				@lang('migrainediary::migraine_diary.add_to_google_sheets')
			</button>
		</div>
	</div>
@else
	@lang('migrainediary::migraine_diary.no_rec_found')
@endif
