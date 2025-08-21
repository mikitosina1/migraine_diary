<dialog
	id="{{ $id }}"
	class="fixed inset-0 z-50 hidden w-full h-full p-0 bg-black/40 backdrop-blur-sm transition-opacity duration-300"
	@if(isset($attributes))
		{{ $attributes }}
	@endif
>
	<div
		class="relative rounded-2xl shadow-2xl bg-white dark:bg-gray-900 transform transition-all scale-95 opacity-0 dialog-content">
		@if(isset($title))
			<div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 px-6 py-4">
				<h3 class="text-xl font-semibold text-gray-900 dark:text-white">
					{{ $title }}
				</h3>
				<button type="button" class="modal-close text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
					✕
				</button>
			</div>
		@endif

		<div class="p-6">
			{{ $slot }}
		</div>
	</div>
</dialog>

<style>
	dialog[open] .dialog-content {
		opacity: 1 !important;
		transform: scale(1) !important;
	}
</style>
