<div class="flex flex-col gap-2">
	<!-- Date & Time -->
	<div>
		<label for="start_time" class="p-1 mr-2 text-white">@lang('migrainediary::migraine_diary.start_time'): </label>
		<input type="datetime-local" name="start_time" value="{{ now()->format('Y-m-d\TH:i') }}">
	</div>

	<!-- Pain Level -->
	<div>
		<label for="pain_level" class="p-1 mr-2 text-white">@lang('migrainediary::migraine_diary.pain_level'): </label>
		<select name="pain_level">
			@for($i = 1; $i <= 10; $i++)
				<option value="{{ $i }}">{{ $i }}</option>
			@endfor
		</select>
	</div>
</div>
