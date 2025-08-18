<div class="grid grid-cols-2 gap-2 max-h-60 overflow-y-auto">
	@foreach($meds as $med)
		<label>
			<input type="checkbox" name="meds[]" value="{{ $med['id'] }}">
			{{ $med['name'] }}
		</label>
	@endforeach
</div>
