<div class="flex flex-col gap-2">
	@if(isset($symptoms))
		@foreach($symptoms as $symptom)
			<label class="text-white">
				<input type="checkbox" name="symptoms[]" value="{{ $symptom['id'] }}"
					   @if(isset($attack) && $attack->symptoms->contains($symptom['id'])) checked @endif
				>
				{{ $symptom['name'] }}
			</label>
		@endforeach
	@endif
	@if(isset($userSymptoms))
		@foreach($userSymptoms as $userSymptom)
			<label class="text-white">
				<input type="checkbox" name="userSymptoms[]" value="{{ $userSymptom['id'] }}"
					   @if(isset($attack) && $attack->userSymptoms->contains($userSymptom['id'])) checked @endif
				>
				{{ $userSymptom['name'] }}
			</label>
		@endforeach
	@endif
	<div class="add-new-symptom"><i class="fas fa-plus text-white"></i></div>
</div>
