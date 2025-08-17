<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
	<!-- Date & Time -->
	<div>
		<input type="datetime-local" name="start_time" value="{{ now()->format('Y-m-d\TH:i') }}">
	</div>

	<!-- Pain Level -->
	<div>
		<select name="pain_level">
			@for($i = 1; $i <= 10; $i++)
				<option value="{{ $i }}">{{ $i }}</option>
			@endfor
		</select>
	</div>
</div>
