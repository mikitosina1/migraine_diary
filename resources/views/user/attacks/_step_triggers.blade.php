<div class="flex flex-row flex-wrap gap-2">
	@if(isset($triggers))
		@foreach($triggers as $trigger)
			<label class="inline-flex items-center cursor-pointer">
				<input type="checkbox" class="hidden peer" name="triggers[]" value="{{ $trigger['id'] }}"
					   @if(isset($attack) && $attack->triggers->contains($trigger['id'])) checked @endif>
				<span class="px-3 py-1 rounded-full border border-white text-white
				peer-checked:bg-blue-500 peer-checked:text-white transition-all">
					{{ $trigger['name'] }}
				</span>
			</label>
		@endforeach
	@endif
	@if(isset($userTriggers))
		@foreach($userTriggers as $userTrigger)
			<label class="inline-flex items-center cursor-pointer">
				<input type="checkbox" class="hidden peer" name="userTriggers[]" value="{{ $userTrigger['id'] }}"
					   @if(isset($attack) && $attack->userTriggers->contains($userTrigger['id'])) checked @endif>
				<span class="px-3 py-1 rounded-full border border-white text-white
					peer-checked:bg-blue-500 peer-checked:text-white transition-all">
					{{ $userTrigger['name'] }}
				</span>
			</label>
		@endforeach
	@endif
	<div class="add-new-trigger flex items-center p-4"><i class="fas fa-plus text-white"></i></div>
</div>
