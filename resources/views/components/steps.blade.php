@props(['form_steps' => []])

<div class="steps flex justify-between mb-8 relative">
	@foreach($form_steps as $key => $label)
		<div class="flex items-center relative mr-2">
			<div class="step-circle w-9 h-9 flex items-center justify-center rounded-full border-2
				text-sm font-semibold transition-colors duration-300
				@if($loop->iteration == 1) bg-blue-500 text-white border-blue-500
				@else bg-gray-200 text-gray-600 border-gray-300 @endif
				step-{{ $loop->iteration }}"
				 data-step="{{ $loop->iteration }}"
			>
				{{ $loop->iteration }}
			</div>

			<div class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
				{{ $label }}
			</div>
		</div>
	@endforeach
</div>
