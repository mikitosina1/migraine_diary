<div class="flex flex-col gap-2">
	@foreach($triggers as $trigger)
		<label class="text-white">
			<input type="checkbox" name="triggers[]" value="{{ $trigger['id'] }}">
			{{ $trigger['name'] }}
		</label>
	@endforeach
</div>
