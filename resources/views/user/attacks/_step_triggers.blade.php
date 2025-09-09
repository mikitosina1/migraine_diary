<div class="flex flex-col gap-2">
	@if(isset($triggers))
		@foreach($triggers as $trigger)
			<label class="text-white">
				<input type="checkbox" name="triggers[]" value="{{ $trigger['id'] }}"
					   @if(isset($attack) && $attack->triggers->contains($trigger['id'])) checked @endif
				>
				{{ $trigger['name'] }}
			</label>
		@endforeach
	@endif
	@if(isset($userTriggers))
		@foreach($userTriggers as $userTrigger)
			<label class="text-white">
				<input type="checkbox" name="triggers[]" value="{{ $trigger['id'] }}"
					   @if(isset($attack) && $attack->triggers->contains($trigger['id'])) checked @endif
				>
				{{ $userTrigger['name'] }}
			</label>
		@endforeach
	@endif
	<div class="add-new-trigger"><i class="fas fa-plus text-white"></i></div>
</div>
