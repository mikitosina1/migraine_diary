<div class="grid grid-cols-2 gap-2 max-h-60 overflow-y-auto">
	@foreach($symptoms as $symptom)
		<label>
			<input type="checkbox" name="symptoms[]" value="{{ $symptom['id'] }}">
			{{ $symptom['name'] }}
		</label>
	@endforeach
</div>
