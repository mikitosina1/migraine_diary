<div class="flex flex-col gap-2">
	<!-- Date & Time -->
	<div>
		<label for="start_time" class="p-1 mr-2 text-white">@lang('migrainediary::migraine_diary.start_time'): </label>
		<input type="datetime-local" name="start_time" id="start_time" value="{{ now()->format('Y-m-d\TH:i') }}">
	</div>

	<!-- Pain Level -->
	<div class="flex flex-col gap-2">
		<label class="p-1 mr-2 text-white">
			@lang('migrainediary::migraine_diary.pain_level'):
		</label>

		<div class="flex flex-col w-64 rounded-lg overflow-hidden shadow-lg border border-gray-300">
			@for($i = 10; $i >= 1; $i--)
				<label class="flex items-center cursor-pointer w-full">
					<input type="radio" name="pain_level" value="{{ $i }}" class="hidden peer">

					<div class="flex items-center justify-start w-full h-10 px-3 text-sm font-semibold
					text-white transition
					peer-checked:ring-2 peer-checked:ring-black peer-checked:ring-offset-1"
						 style="background-color: hsl({{ 60 + (6 - $i) * 12 }}, 90%, 40%)">

						<span class="w-6 text-center">{{ $i }}</span>
						<span class="ml-2 flex-1">{{ __('migrainediary::migraine_diary.pain_level_'.$i) }}</span>
					</div>
				</label>
			@endfor
		</div>
	</div>

</div>
