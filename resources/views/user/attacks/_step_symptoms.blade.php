<div class="flex flex-row flex-wrap gap-2">
	@if(isset($symptoms))
		@foreach($symptoms as $symptom)
			<label class="inline-flex items-center cursor-pointer">
				<input type="checkbox" class="hidden peer" name="symptoms[]" value="{{ $symptom['id'] }}"
					   @if(isset($attack) && $attack->symptoms->contains($symptom['id'])) checked @endif>
				<span class="px-3 py-1 rounded-full border border-white text-white
				peer-checked:bg-blue-500 peer-checked:text-white transition-all">
					{{ $symptom['name'] }}
				</span>
			</label>
		@endforeach
	@endif
	@if(isset($userSymptoms))
		@foreach($userSymptoms as $userSymptom)
			<label class="inline-flex items-center cursor-pointer ">
				<input type="checkbox" class="hidden peer" name="userSymptoms[]" value="{{ $userSymptom['id'] }}"
					   @if(isset($attack) && $attack->userSymptoms->contains($userSymptom['id'])) checked @endif>
				<span class="px-3 py-1 rounded-full border border-white text-white
					peer-checked:bg-blue-500 peer-checked:text-white transition-all">
				{{ $userSymptom['name'] }}
				</span>
			</label>
		@endforeach
	@endif
	<div class="add-new-symptom flex items-center p-4"><i class="fas fa-plus text-white"></i></div>
</div>
