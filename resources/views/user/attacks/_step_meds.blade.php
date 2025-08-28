<div class="flex flex-col gap-2">
	@foreach($meds as $med)
		<label class="text-white">
			<input type="checkbox" name="meds[]" value="{{ $med['id'] }}"
				   @if(isset($attack) && $attack->meds->contains($med['id'])) checked @endif
			>
			{{ $med['name'] }}
		</label>
	@endforeach
</div>
