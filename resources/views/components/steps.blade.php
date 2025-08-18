@props(['form_steps' => []])

<div class="steps flex items-center justify-between mb-6">
	@foreach($form_steps as $key => $label)
		<div class="step-item flex-1 flex items-center">
			<div class="step-circle w-8 h-8 flex items-center justify-center rounded-full border
				text-sm font-semibold
				bg-gray-200 dark:bg-gray-700
				text-gray-600 dark:text-gray-300"
				data-step="{{ $loop->iteration }}"
			>
				{{ $loop->iteration }}
			</div>

			<div class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
				{{ $label }}
			</div>

			@if(!$loop->last)
				<div class="flex-1 h-0.5 bg-gray-300 dark:bg-gray-600 mx-4"></div>
			@endif
		</div>
	@endforeach
</div>
