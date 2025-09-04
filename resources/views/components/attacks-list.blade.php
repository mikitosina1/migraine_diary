@php
	use Carbon\Carbon;
	$currentRange = $currentRange ?? 'year';
@endphp

@if($attacks->count())
	<div class="list-header flex flex-row justify-between items-center p-4">
		<div class="filter-block mb-4">
			<select id="list-attack-range" class="bg-gray-800 text-white py-2 rounded">
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

	<div class="list grid gap-4 p-4">
		@foreach($attacks as $attack)
			<div x-data="{ open: false }" class="migraine-list-item rounded-md bg-gray-800 text-white shadow">
				{{-- Block Title --}}
				<div
					@click="open = !open"
					class="statistic-header w-full flex justify-between items-center p-4 text-left focus:outline-none"
				>
					<div class="flex flex-col gap-2 mr-2">
						<span>
							<strong>@lang('migrainediary::migraine_diary.start_time'):</strong>
							{{ Carbon::parse($attack->start_time)->format('d.m.Y H:i') }}
						</span>
						@if($attack->end_time)
							<span>
								<strong>@lang('migrainediary::migraine_diary.end_time'):</strong>
								{{ Carbon::parse($attack->end_time)->format('d.m.Y H:i') }}
							</span>
						@endif
					</div>
					<div class="short-info flex items-center gap-3">
						{{-- pain level span --}}
						<span class="px-2 py-1 rounded text-sm"
							  style="background-color: hsl({{ 60 + (6 - $attack->pain_level) * 12 }}, 90%, 40%)">
							@lang('migrainediary::migraine_diary.pain_level'): {{ $attack->pain_level }}
						</span>
						{{-- end attack button --}}
						@if($attack->end_time == null)
							<button
								data-attack-id="{{ $attack->id }}"
								class="end-attack-button flex flex-row gap-2 items-center"
								title=" {{ __('migrainediary::migraine_diary.end_attack') }}">
								<i class="fas fa-check-circle"></i>
							</button>
						@endif
						{{-- expand/collapse button --}}
						<svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
							 viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
						</svg>
						<svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
							 viewBox="0 0 24 24" stroke="currentColor">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
						</svg>
					</div>
				</div>

				{{-- Content --}}
				<div x-show="open" x-collapse class="list-item-content p-4 border-t border-gray-700">
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

					<div class="list-item-buttons flex flex-row items-center justify-end space-x-2 mt-4">
						<!-- Delete -->
						<button
							type="button"
							class="delete-btn mr-2"
							data-attack-id="{{ $attack->id }}"
							title="@lang('migrainediary::migraine_diary.delete')"
						>
							@lang('migrainediary::migraine_diary.delete')
							<i class="fas fa-trash text-red-500"></i>
						</button>
						<!-- Edit -->
						<button
							class="edit-btn px-2 py-1 bg-gray-500 rounded-md"
							data-attack-id="{{ $attack->id }}"
							title="@lang('migrainediary::migraine_diary.update')"
						>
							@lang('migrainediary::migraine_diary.update')
							<i class="fas fa-edit text-green-600"></i>
						</button>
					</div>
				</div>
			</div>
		@endforeach
	</div>
@else
	@lang('migrainediary::migraine_diary.no_rec_found')
@endif
