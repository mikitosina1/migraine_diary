<div class="flex flex-col gap-2">
	@foreach($symptoms as $symptom)
		<label>
			<input type="checkbox" name="symptoms[]" value="{{ $symptom['id'] }}">
			{{ $symptom['name'] }}
		</label>
	@endforeach
</div>
