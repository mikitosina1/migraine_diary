<div class="flex flex-col gap-2">
	@foreach($symptoms as $symptom)
		<label class="text-white">
			<input type="checkbox" name="symptoms[]" value="{{ $symptom['id'] }}">
			{{ $symptom['name'] }}
		</label>
	@endforeach
</div>
