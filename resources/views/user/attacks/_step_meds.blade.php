<div class="flex flex-col gap-2">
	@if(isset($meds))
		@foreach($meds as $med)
			<label class="text-white">
				<input type="checkbox" name="meds[]" value="{{ $med['id'] }}"
					   @if(isset($attack) && $attack->meds->contains($med['id'])) checked @endif
				>
				{{ $med['name'] }}
			</label>
		@endforeach
	@endif
	@if(isset($userMeds))
		@foreach($userMeds as $userMed)
			<label class="text-white">
				<input type="checkbox" name="userMeds[]" value="{{ $userMed['id'] }}"
					   @if(isset($attack) && $attack->userMed->contains($userMed['id'])) checked @endif
				>
				{{ $userMed['name'] }}
			</label>
		@endforeach
	@endif
	<div class="add-new-med"><i class="fas fa-plus text-white"></i></div>
</div>
