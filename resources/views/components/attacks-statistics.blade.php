@php
	use Carbon\Carbon;
	$currentRange = $currentRange ?? 'year';
@endphp
@if($attacks)
	<div class="statistic-header flex flex-row justify-between items-center p-4">
		<div class="filter-block mb-4">
			<select id="statistic-attack-range" class="bg-gray-800 text-white py-2 rounded">
				<option value="year" {{ $currentRange === 'year' ? 'selected' : '' }}>
					@lang('migrainediary::migraine_diary.last_year')
				</option>
				<option value="3months" {{ $currentRange === '3months' ? 'selected' : '' }}>
					@lang('migrainediary::migraine_diary.last_3_months')
				</option>
				<option value="month" {{ $currentRange === 'month' ? 'selected' : '' }}>
					@lang('migrainediary::migraine_diary.last_month')
				</option>
			</select>
		</div>
	</div>
	<div class="statistic grid gap-4 p-4">

	</div>
@else
	@lang('migrainediary::migraine_diary.no_rec_found')
@endif
