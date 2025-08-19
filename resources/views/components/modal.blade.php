<dialog
	id="{{ $id }}"
	class="fixed inset-0 z-50 hidden w-full h-full p-0 bg-black bg-opacity-50 backdrop:bg-black backdrop:bg-opacity-50"
@if(isset($attributes)) {{ $attributes }} @endif
>
	<div class="relative mx-auto my-8 p-2 max-w-2xl rounded-lg shadow-lg bg-white dark:bg-gray-800">
		@if(isset($title))
			<h3 class="text-lg font-medium text-gray-900 dark:text-white mt-2 ml-2">
				{{ $title }}
			</h3>
		@endif
		<div class="p-6 flex flex-row">
			{{ $slot }}
		</div>
	</div>
</dialog>
