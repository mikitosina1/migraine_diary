@php
	use Carbon\Carbon;
@endphp

@if($attacks->count())
	<div class="list-header flex flex-row justify-between items-center p-4">
		<div class="text-white mr-2">
			@lang('migrainediary::migraine_diary.count'): {{ $attacks->count() }}
		</div>
		<div class="text-white ml-2">
			@lang('migrainediary::migraine_diary.count_by_month'):
			{{ $attacks->filter(fn($a) => Carbon::parse($a->start_time)->isCurrentMonth())->count() }}
		</div>
	</div>

	<div class="list grid gap-4 p-4">
		@foreach($attacks as $attack)
			<div x-data="{ open: false }" class="rounded-md bg-gray-800 text-white shadow">
				{{-- Block Title --}}
				<button
					@click="open = !open"
					class="w-full flex justify-between items-center p-4 text-left focus:outline-none"
				>
					<div class="mr-2">
						<strong>@lang('migrainediary::migraine_diary.start_time'):</strong>
						{{ Carbon::parse($attack->start_time)->format('d.m.Y H:i') }}
						@if($attack->end_time)
							<span class="ml-4">
								<strong>@lang('migrainediary::migraine_diary.end_time'):</strong>
								{{ Carbon::parse($attack->end_time)->format('d.m.Y H:i') }}
							</span>
						@endif
					</div>
					<div class="flex items-center gap-3">
						<span class="px-2 py-1 rounded text-sm" style="background-color: hsl({{ 60 + (6 - $attack->pain_level) * 12 }}, 90%, 40%)">
							@lang('migrainediary::migraine_diary.pain_level'): {{ $attack->pain_level }}
						</span>
						<svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
							 viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
						</svg>
						<svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
							 viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
						</svg>
					</div>
				</button>

				{{-- Content --}}
				<div x-show="open" x-collapse class="p-4 border-t border-gray-700">
					@if($attack->notes)
						<div class="mt-2 italic">"{{ $attack->notes }}"</div>
					@endif

					@if($attack->symptoms->count())
						<div class="mt-2">
							<strong>@lang('migrainediary::migraine_diary.symptoms'):</strong>
							{{ $attack->symptoms->pluck('name')->join(', ') }}
						</div>
					@endif

					@if($attack->triggers->count())
						<div class="mt-2">
							<strong>@lang('migrainediary::migraine_diary.triggers'):</strong>
							{{ $attack->triggers->pluck('name')->join(', ') }}
						</div>
					@endif

					@if($attack->meds->count())
						<div class="mt-2">
							<strong>@lang('migrainediary::migraine_diary.meds'):</strong>
							{{ $attack->meds->pluck('name')->join(', ') }}
						</div>
					@endif
				</div>
			</div>
		@endforeach
	</div>
@endif
