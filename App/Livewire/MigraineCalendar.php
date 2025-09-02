<?php

namespace Modules\MigraineDiary\App\Livewire;

use Illuminate\View\View;
use Livewire\Component;
use Modules\MigraineDiary\App\Models\MigraineAttack;
use Illuminate\Support\Carbon;

/**
 * Class MigraineCalendar
 *
 * @package Modules\MigraineDiary\App\Livewire
 *
 * @property string $monthName title of the month
 * @property array $calendarDays array of days in the month
 * @property string $currentDate YYYY-MM
 * @property array $attacks array of attacks for the current month
 * @property string $selectedDay key of the selected day
 */
class MigraineCalendar extends Component
{
	/** @var string $currentDate YYYY-MM format */
	public $currentDate;
	/** @var array $attacks array of attacks for the current month */
	public $attacks = [];
	/** @var string $selectedDay key of the selected day */
	public $selectedDay = null;

	/**
	 * Mount the component
	 * @return void
	 */
	public function mount(): void
	{
		$this->currentDate = now()->format('Y-m');
		$this->loadAttacks();
	}

	/**
	 * Load attacks for the current month
	 * @return void
	 */
	public function loadAttacks(): void
	{
		try {
			$attacks = MigraineAttack::where('user_id', auth()->id())
				->whereYear('start_time', substr($this->currentDate, 0, 4))
				->whereMonth('start_time', substr($this->currentDate, 5, 2))
				->with(['symptoms', 'triggers', 'meds'])
				->get();

			$attacksByDay = [];

			foreach ($attacks as $attack) {
				$day = $attack->start_time->format('Y-m-d');

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

		} catch (\Exception $e) {
			$this->attacks = [];
			logger()->error('Error loading attacks: ' . $e->getMessage());
		}
	}

	/**
	 * Change the month
	 * @param string $direction 'next' or 'prev'
	 * @return void
	 */
	public function changeMonth(string $direction): void
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

	/**
	 * Go today
	 * @return void
	 */
	public function goToToday(): void
	{
		$this->currentDate = now()->format('Y-m');
		$this->loadAttacks();
		$this->selectedDay = null;
	}

	/**
	 * Select a day
	 * @param string $day key of the day
	 * @return void
	 */
	public function selectDay(string $day): void
	{
		$this->selectedDay = $day;
	}

	/**
	 * Get the name of the month
	 * @return string
	 */
	public function getMonthNameProperty(): string
	{
		return Carbon::createFromFormat('Y-m', $this->currentDate)
			->translatedFormat('F Y');
	}

	/**
	 * Render the component
	 * @return View
	 */
	public function render(): View
	{
		return view('migrainediary::livewire.migraine-calendar', [
			'monthName' => $this->monthName,
			'calendarDays' => $this->prepareCalendarDays()
		]);
	}

	/**
	 * Prepare the calendar days
	 * @return array
	 */
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
			$dayKey = $date->format('Y-m-') . str_pad($day, 2, '0', STR_PAD_LEFT);
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
