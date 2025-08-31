<?php

namespace Modules\MigraineDiary\App\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Modules\MigraineDiary\App\Models\MigraineAttack;
use Illuminate\Support\Carbon;

class MigraineCalendar extends Component
{
	public $currentDate;
	public $attacks = [];
	public $selectedDay = null;

	public function mount(): void
	{
		$this->currentDate = now()->format('Y-m');
		$this->loadAttacks();
	}

	public function loadAttacks(): void
	{
		$attacks = MigraineAttack::where('user_id', auth()->id())
			->whereYear('start_time', substr($this->currentDate, 0, 4))
			->whereMonth('start_time', substr($this->currentDate, 5, 2))
			->with(['symptoms', 'triggers', 'meds'])
			->get();

		$attacksByDay = [];

		foreach ($attacks as $attack) {
			$day = $attack->start_time->format('d');

			if (!isset($attacksByDay[$day])) {
				$attacksByDay[$day] = [];
			}

			$attacksByDay[$day][] = [
				'id' => $attack->id,
				'pain_level' => $attack->pain_level,
				'symptoms' => $attack->symptoms->pluck('name')->toArray(),
				'triggers' => $attack->triggers->pluck('name')->toArray(),
				'time' => $attack->start_time->format('H:i')
			];
		}

		$this->attacks = $attacksByDay;
	}

	public function changeMonth($direction): void
	{
		$date = Carbon::createFromFormat('Y-m', $this->currentDate);

		if ($direction === 'next') {
			$date->addMonth();
		} else {
			$date->subMonth();
		}

		$this->currentDate = $date->format('Y-m');
		$this->loadAttacks();
		$this->selectedDay = null;
	}

	public function goToToday(): void
	{
		$this->currentDate = now()->format('Y-m');
		$this->loadAttacks();
		$this->selectedDay = null;
	}

	public function selectDay($day): void
	{
		$this->selectedDay = $day;
	}

	public function getMonthNameProperty(): string
	{
		return Carbon::createFromFormat('Y-m', $this->currentDate)
			->translatedFormat('F Y');
	}

	public function render(): View
	{
		return view('migrainediary::livewire.migraine-calendar', [
			'monthName' => $this->monthName,
			'calendarDays' => $this->prepareCalendarDays()
		]);
	}

	protected function prepareCalendarDays(): array
	{
		$date = Carbon::createFromFormat('Y-m', $this->currentDate);
		$daysInMonth = $date->daysInMonth;
		$startDay = $date->startOfMonth()->dayOfWeekIso; // 1 (пн) - 7 (вс)

		$days = [];

		// empty fields before the first day of the month
		for ($i = 1; $i < $startDay; $i++) {
			$days[] = ['type' => 'empty'];
		}

		for ($day = 1; $day <= $daysInMonth; $day++) {
			$dayKey = str_pad($day, 2, '0', STR_PAD_LEFT);
			$hasAttack = isset($this->attacks[$dayKey]);

			$days[] = [
				'type' => 'day',
				'number' => $day,
				'key' => $dayKey,
				'has_attack' => $hasAttack,
				'attacks' => $hasAttack ? $this->attacks[$dayKey] : [],
				'is_selected' => $this->selectedDay === $dayKey
			];
		}

		return $days;
	}

}
