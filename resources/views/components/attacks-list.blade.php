@php
	use Carbon\Carbon;
	$currentRange = $currentRange ?? 'month';
	$currentPainLevel = $currentPainLevel ?? 'all';
@endphp

@if($attacks->count())
	<div class="list-header flex flex-row justify-between items-center p-4">
		<div class="filter-block mb-4">
			<select id="list-attack-range" class="bg-gray-800 text-white py-2 rounded">
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

			@if($currentPainLevel)
				<!-- Pain Level Filter -->
				<select id="list-pain-level" class="bg-gray-800 text-white py-2 rounded">
					<option value="all">@lang('migrainediary::migraine_diary.all_pain_lvl')</option>
					@for($i = 1; $i <= 10; $i++)
						<option value="{{ $i }}" {{ $currentPainLevel == $i ? 'selected' : '' }}>
							@lang('migrainediary::migraine_diary.level') {{ $i }}
						</option>
					@endfor
				</select>
			@endif

			<!-- Reset Filters Button -->
			<button id="reset-filters" class="bg-gray-600 text-white rounded hover:bg-gray-700">
				@lang('migrainediary::migraine_diary.reset_filters')
			</button>
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
					<div class="date-block flex flex-col gap-2 mr-2">
						<span class="flex flex-row justify-between">
							<strong class="mr-1">@lang('migrainediary::migraine_diary.start_time'):</strong>
							{{ Carbon::parse($attack->start_time)->format('d.m.Y H:i') }}
						</span>
						@if($attack->end_time)
							<span class="flex flex-row justify-between">
								<strong>@lang('migrainediary::migraine_diary.end_time'):</strong>
								{{ Carbon::parse($attack->end_time)->format('d.m.Y H:i') }}
							</span>
						@endif
					</div>
					<div class="short-info flex items-center gap-3">
						{{-- pain level span --}}
						<span class="px-2 py-1 rounded text-sm border-2"
							  style="border-color: hsl({{ 60 + (6 - $attack->pain_level) * 12 }}, 90%, 40%)">
							@lang('migrainediary::migraine_diary.pain_level'): {{ $attack->pain_level }}
						</span>
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
					<div class="main-content-block flex flex-row justify-between">
						<div class="content-block flex flex-col">
							@if($attack->symptoms->count())
								<div class="mt-2">
									<strong>@lang('migrainediary::migraine_diary.symptoms'):</strong>
									{{ $attack->symptoms->pluck('name')->join(', ') }}
									@if($attack->userSymptoms->count())
										, {{ $attack->userSymptoms->pluck('name')->join(', ') }}
									@endif
								</div>
							@elseif($attack->userSymptoms->count())
								<div class="mt-2">
									<strong>@lang('migrainediary::migraine_diary.symptoms'):</strong>
									{{ $attack->userSymptoms->pluck('name')->join(', ') }}
								</div>
							@endif

							@if($attack->triggers->count())
								<div class="mt-2">
									<strong>@lang('migrainediary::migraine_diary.triggers'):</strong>
									{{ $attack->triggers->pluck('name')->join(', ') }}
									@if($attack->userTriggers->count())
										, {{ $attack->userTriggers->pluck('name')->join(', ') }}
									@endif
								</div>
							@elseif($attack->userTriggers->count())
								<div class="mt-2">
									<strong>@lang('migrainediary::migraine_diary.triggers'):</strong>
									{{ $attack->userTriggers->pluck('name')->join(', ') }}
								</div>
							@endif

							@if($attack->meds->count())
								<div class="mt-2">
									<strong>@lang('migrainediary::migraine_diary.meds'):</strong>
									{{ $attack->meds->pluck('name')->join(', ') }}
									@if($attack->userMeds->count())
										, {{ $attack->userMeds->pluck('name')->join(', ') }}
									@endif
								</div>
							@elseif($attack->userMeds->count())
								<div class="mt-2">
									<strong>@lang('migrainediary::migraine_diary.meds'):</strong>
									{{ $attack->userMeds->pluck('name')->join(', ') }}
								</div>
							@endif
						</div>
						<div class="controls flex flex-col items-center space-x-2">
							<!-- Delete -->
							<button
								type="button"
								class="delete-btn rounded-md"
								data-attack-id="{{ $attack->id }}"
								title="@lang('migrainediary::migraine_diary.delete')"
							>
								<i class="fas fa-trash"></i>
							</button>
							<!-- Edit -->
							<button
								class="edit-btn rounded-md"
								data-attack-id="{{ $attack->id }}"
								title="@lang('migrainediary::migraine_diary.update')"
							>
								<i class="fas fa-edit"></i>
							</button>

							<!-- end attack button -->
							@if($attack->end_time == null)
								<button
									data-attack-id="{{ $attack->id }}"
									class="end-attack-button"
									title=" {{ __('migrainediary::migraine_diary.end_attack') }}">
									<i class="fas fa-check-double"></i>
								</button>
							@endif
						</div>
					</div>
				</div>
			</div>
		@endforeach
	</div>
@else
	@lang('migrainediary::migraine_diary.no_rec_found')
@endif
