<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
	<div class="modal-container bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md mx-auto mt-20 p-6">
		<div class="flex justify-between items-center mb-4">
			<h2 id="modalTitle"
				data-add="@lang('migrainediary::admin.add')"
				data-edit="@lang('migrainediary::admin.edit_item')"
				class="text-lg font-bold"></h2>
			<button class="modal-close">&times;</button>
		</div>

		<form id="editForm">
			@csrf
			<input type="hidden" id="itemId" name="id">
			<input type="hidden" id="itemType" name="type">

			<div class="mb-4 code-block">
				<div class="code-label">
					<label for="itemCode" class="block mb-2">@lang('migrainediary::admin.code')*</label>
					<i class="fas fa-info-circle text-gray-400 cursor-pointer ml-1"
					   title="@lang('migrainediary::admin.code_tooltip')"></i>
				</div>
				<div id="codeError" class="text-red-500 text-sm mt-1 hidden"></div>
				<input type="text" id="itemCode" name="code" required
					   class="w-full px-3 py-2 border rounded-md dark:bg-gray-700">
			</div>

			<!-- languages -->
			@foreach($locales ?? config('app.locales') as $locale)
				<div class="mb-4">
					<label for="name_{{ $locale }}" class="block mb-2">
						@lang('migrainediary::admin.name') ({{ strtoupper($locale) }})
					</label>
					<input type="text" id="name_{{ $locale }}" name="translations[{{ $locale }}][name]"
						   class="w-full px-3 py-2 border rounded-md dark:bg-gray-700">
				</div>
			@endforeach

			<div class="flex justify-end space-x-3 mt-6">
				<button type="button" class="modal-close px-4 py-2 border rounded-md">
					@lang('migrainediary::admin.cancel')
				</button>
				<button type="submit"
						id="modalSaveBtn"
						data-save="@lang('migrainediary::admin.save')"
						data-update="@lang('migrainediary::admin.update')"
						class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
				</button>
			</div>
		</form>
	</div>
</div>
