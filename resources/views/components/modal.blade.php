<dialog
	id="{{ $id }}"
	class="fixed inset-0 z-50 hidden w-full h-full p-0 bg-black bg-opacity-50 backdrop:bg-black backdrop:bg-opacity-50"
@if(isset($attributes)) {{ $attributes }} @endif
>
	<div class="relative mx-auto my-8 max-w-2xl rounded-lg shadow-lg bg-white dark:bg-gray-800">
		<div class="p-6">
			{{ $slot }}
		</div>
	</div>
</dialog>
