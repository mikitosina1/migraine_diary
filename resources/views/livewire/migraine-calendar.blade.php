@php
	$daysShort = __('migrainediary::migraine_diary.short_days');
@endphp
<div>
	<!-- Navigation -->
	<div class="flex justify-between items-center mb-4">
		<div class="flex space-x-2">
			<button wire:click="changeMonth('prev')" class="px-3 py-1 bg-blue-600 text-white rounded-md">
				←
			</button>
			<button wire:click="changeMonth('next')" class="px-3 py-1 bg-blue-600 text-white rounded-md">
				→
			</button>
			<button wire:click="goToToday" class="px-3 py-1 bg-gray-600 text-white rounded-md">
				@lang('migrainediary::migraine_diary.today')
			</button>
		</div>
		<h3 class="text-lg font-semibold text-white">{{ $monthName }}</h3>
	</div>

	<!-- Calendar -->
	<div class="grid grid-cols-7 gap-2 mb-4">
		<!-- Days -->
		@foreach($daysShort as $day)
			<div class="text-center font-semibold p-2 text-sm text-white">{{ $day }}</div>
		@endforeach

		@foreach($calendarDays as $day)
			@if($day['type'] === 'empty')
				<div class="p-2"></div>
			@else
				<div class="border rounded-md p-2 text-center cursor-pointer transition-colors bg-blue-100
					{{ $day['is_selected'] ? 'bg-blue-100 border-blue-300' : '' }}
					{{ $day['has_attack'] ? 'bg-red-50 border-red-200 hover:bg-red-100' : 'hover:bg-gray-50' }}"
					 wire:click="selectDay('{{ $day['key'] }}')">

					<div class="text-sm font-medium">{{ $day['number'] }}</div>

					@if($day['has_attack'])
						<div class="text-xs text-red-600 mt-1">
							⚡ {{ count($day['attacks']) }}
						</div>
					@endif
				</div>
			@endif
		@endforeach
	</div>

	<!-- Details if clicked day has attacks -->
	@if($selectedDay && isset($attacks[$selectedDay]))
		<div class=" bg-blue-100 rounded-lg p-4 mb-4">
			<h4 class="font-semibold mb-2">@lang('migrainediary::migraine_diary.attacks_for') {{ $selectedDay }} :</h4>
			@foreach($attacks[$selectedDay] as $attack)
				<div class="mb-3 p-2 bg-white rounded border">
					<div class="flex justify-between items-start">
						<span class="font-medium">@lang('migrainediary::migraine_diary.pain_level'): {{ $attack['pain_level'] }}/10</span>
						<span class="text-sm text-gray-500">{{ $attack['time'] }}</span>
					</div>

					@if(!empty($attack['symptoms']))
						<div class="text-sm mt-1">
							<span class="font-medium">@lang('migrainediary::migraine_diary.symptoms'):</span>
							{{ implode(', ', $attack['symptoms']) }}
						</div>
					@endif

					@if(!empty($attack['triggers']))
						<div class="text-sm mt-1">
							<span class="font-medium">@lang('migrainediary::migraine_diary.triggers'):</span>
							{{ implode(', ', $attack['triggers']) }}
						</div>
					@endif
				</div>
			@endforeach
		</div>
	@endif
</div>
