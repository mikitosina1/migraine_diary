<div class="flex flex-col gap-2">
	@foreach($symptoms as $symptom)
		<label class="text-white">
			<input type="checkbox" name="symptoms[]" value="{{ $symptom['id'] }}"
				   @if(isset($attack) && $attack->meds->contains($symptom['id'])) checked @endif
			>
			{{ $symptom['name'] }}
		</label>
	@endforeach
</div>
