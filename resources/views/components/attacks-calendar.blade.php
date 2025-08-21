<div class="flex justify-between items-center mb-4">
	<!-- navigation buttons -->
	<div class="flex space-x-2">
		<button id="prev-month" class="px-3 py-1 text-white border rounded-md">
			<i class="fas fa-chevron-left"></i>
		</button>
		<button id="next-month" class="px-3 py-1 text-white border rounded-md">
			<i class="fas fa-chevron-right"></i>
		</button>
		<button id="today-btn" class="px-3 py-1 text-white border rounded-md">
			@lang('migrainediary::migraine_diary.today')
		</button>
	</div>
</div>
@if($attacks)

@endif
