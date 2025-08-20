<div class="flex flex-col gap-2">
	@foreach($meds as $med)
		<label class="text-white">
			<input type="checkbox" name="meds[]" value="{{ $med['id'] }}">
			{{ $med['name'] }}
		</label>
	@endforeach
</div>
