<div class="flex flex-row flex-wrap gap-2">
	@if(isset($meds))
		@foreach($meds as $med)
			<label class="inline-flex items-center cursor-pointer">
				<input type="checkbox" class="hidden peer" name="meds[]" value="{{ $med['id'] }}"
					@if(isset($attack) && $attack->meds->contains($med['id'])) checked @endif>
				<span class="px-3 py-1 rounded-full border border-white text-white
					peer-checked:bg-blue-500 peer-checked:text-white transition-all">
					{{ $med['name'] }}
				</span>
			</label>
		@endforeach
	@endif
	@if(isset($userMeds))
		@foreach($userMeds as $userMed)
			<label class="inline-flex items-center cursor-pointer">
				<input type="checkbox" class="hidden peer" name="userMed[]" value="{{ $userMed['id'] }}"
					@if(isset($attack) && $attack->userMed->contains($userMed['id'])) checked @endif>
				<span class="px-3 py-1 rounded-full border border-white text-white
					peer-checked:bg-blue-500 peer-checked:text-white transition-all">
					{{ $userMed['name'] }}
				</span>
			</label>
		@endforeach
	@endif
	<div class="add-new-med flex items-center p-4"><i class="fas fa-plus text-white"></i></div>
</div>
