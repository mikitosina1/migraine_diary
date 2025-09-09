@vite(['Modules/MigraineDiary/resources/assets/js/diary_main.js', 'Modules/MigraineDiary/resources/assets/sass/diary_main.scss'])
<div class="dashboard-widget migraine-diary cloud-widget-item  {{ $activeAttacks->count() > 0 ? '' : 'minimized' }}">
	<h3 class="text-white ">@lang('migrainediary::migraine_diary.widget_title')</h3>

	<div class="attacks-box flex flex-row gap-4">
		@if($thisMonth)
			<div class="attacks-list p-2">
				<div class="summary py-2">
					<p class="text-white">
						@lang('migrainediary::migraine_diary.last_month'):
						<strong>{{ $thisMonth->count() }}</strong>
					</p>
				</div>
				<ul class="text-white">
					@forelse($thisMonth as $attack)
						<li>
							{{ $attack->created_at->format('d.m.Y') }} :
							<span class="pain_level"
								  style="color: hsl({{ 60 + (6 - $attack->pain_level) * 12 }}, 90%, 40%)">
							{{ $attack->pain_level }}/10
						</span>
						</li>
					@empty
						<li>@lang('migrainediary::migraine_diary.no_attacks_this_month')</li>
					@endforelse
				</ul>
			</div>
		@endif
		@if($activeAttacks && $activeAttacks->count() > 0)
			<div class="active-attacks p-2">
				<div class="summary aa py-2">
					<h3 class="text-white">
						@lang('migrainediary::migraine_diary.active_attacks_this_month'): {{ $activeAttacks->count() }}
					</h3>
				</div>
				<ul class="text-white">
					@forelse($activeAttacks as $attack)
						<li>
							{{ $attack->created_at->format('d.m.Y') }} -
							<span class="attack-loading">
							<span>.</span><span>.</span><span>.</span>
						</span>
							{{-- finish migraine --}}
							<form action="{{ route('user.migraine-diary.attacks.end', $attack->id) }}"
								  method="POST"
								  class="inline-block ml-2">
								@csrf
								<button type="submit" class="end-attack-button flex flex-row gap-2 items-center"
										title=" {{ __('migrainediary::migraine_diary.end_attack') }}"
								>
									<i class="fas fa-check-circle"></i>
									<p>@lang('migrainediary::migraine_diary.end_attack')</p>
								</button>
							</form>
						</li>
					@empty
						<li>@lang('migrainediary::migraine_diary.no_attacks_this_month')</li>
					@endforelse
				</ul>
			</div>
		@endif
	</div>
	<div class="module-link text-white flex flex-row gap-2 items-center justify-end px-3">
		<a href="{{ route('user.migraine-diary.resource.index') }}"
		   class="to-diary">
			@lang('migrainediary::migraine_diary.to_diary')
		</a>
	</div>
</div>
