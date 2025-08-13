<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
	<!-- search bar -->
	<div class="p-4 border-b dark:border-gray-700 flex justify-between">
		<input type="text" placeholder="@lang('migrainediary::admin.search')"
			   class="search-input px-3 py-2 border rounded-md w-64"
			   data-type="{{ $type }}">

		<button class="add-item-btn px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
				data-type="{{ $type }}">
			@lang('migrainediary::admin.add')
		</button>
	</div>

	<!-- list of items -->
	<div class="list-block overflow-y-auto max-h-[calc(100vh-200px)]">
		<ul class="divide-y dark:divide-gray-700">
			@foreach($items as $item)
				<li class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 flex justify-between items-center group">
					<div>
						<span class="font-medium">{{ $item['name'] }}</span>
						<span class="text-sm text-gray-500 ml-2">{{ $item['code'] }}</span>
					</div>
					<div class="opacity-0 group-hover:opacity-100 transition-opacity">
						<button class="edit-item-btn px-2 py-1 text-blue-600 hover:text-blue-800"
								data-id="{{ $item['id'] }}"
								data-type="{{ $type }}">
							<i class="fas fa-edit"></i>
						</button>
						<button class="delete-item-btn px-2 py-1 text-red-600 hover:text-red-800 ml-2"
								data-id="{{ $item['id'] }}"
								data-type="{{ $type }}">
							<i class="fas fa-trash"></i>
						</button>
					</div>
				</li>
			@endforeach
		</ul>
	</div>
</div>
