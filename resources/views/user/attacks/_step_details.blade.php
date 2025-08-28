<div class="flex flex-col gap-2">
	<!-- Date & Time -->
	<div>
		<label for="start_time" class="p-1 mr-2 text-white">@lang('migrainediary::migraine_diary.start_time'): </label>
		<input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time', isset($attack) ?
			$attack->start_time->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
		>
	</div>

	<!-- Pain Level -->
	<div class="flex flex-col gap-2">
		<label class="p-1 mr-2 text-white">
			@lang('migrainediary::migraine_diary.pain_level'):
		</label>

		<div class="flex flex-col w-64 rounded-lg overflow-hidden shadow-lg border border-gray-300">
			@for($i = 10; $i >= 1; $i--)
				<label class="flex items-center cursor-pointer w-full">
					<input type="radio" name="pain_level" value="{{ $i }}" class="hidden peer"
						   @if(isset($attack) && $attack->pain_level == $i) checked @endif
					>

					<div class="flex items-center justify-start w-full h-10 px-3 text-sm font-semibold
						text-white transition-all duration-300
						peer-checked:outline peer-checked:outline-4 peer-checked:outline-white
						peer-checked:outline-offset-[-2px] peer-checked:shadow-lg"
						 style="background-color: hsl({{ 60 + (6 - $i) * 12 }}, 90%, 40%)"
					>
						<span class="w-6 text-center">{{ $i }}</span>
						<span class="ml-2 flex-1">{{ __('migrainediary::migraine_diary.pain_level_'.$i) }}</span>
					</div>
				</label>
			@endfor
		</div>
	</div>

</div>
