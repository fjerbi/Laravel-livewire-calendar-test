<?php
namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;

class CalendarComponent extends Component
{

    public $sessionDuration = 60;
    public $currentDate;
    public $weekDates = [];
    public $selectedStartDate;
    public $selectedEndDate;
    public $sessionNames = [];
    public $weekBookedSessions = [];

    public function mount()
    {
        $this->currentDate = Carbon::now()->startOfWeek(); 
        $this->initializeWeekDates();
        $this->initializeWeekBookedSessions();
    }

    public function initializeWeekDates()
    {
        $this->weekDates = [];
        for ($i = 0; $i <= 6; $i++) {
            $this->weekDates[] = $this->currentDate->copy()->addDays($i);
        }
    }


    public function initializeWeekBookedSessions()
    {
        for ($i = 0; $i <= 6; $i++) {
            $this->weekBookedSessions[$i] = [];
        }
    }
    public function goToNextWeek()
    {
        $this->currentDate->addWeek();
        $this->initializeWeekDates();

    }
    public function goToPreviousWeek()
    {
        $this->currentDate->subWeek();
        $this->initializeWeekDates();

    }

    public function selectDate($selectedDate)
    {
        if (!$this->selectedStartDate) {
            $this->selectedStartDate = $selectedDate;
        } elseif (!$this->selectedEndDate) {
            $this->selectedEndDate = $selectedDate;
        }
    }

    public function render()
    {
        return view('calendar-component')->with([
            'weekDates' => $this->weekDates,
            'currentDate' => $this->currentDate,
            'weekBookedSessions' => $this->weekBookedSessions,
        ]);
    }

    public function bookSession($dateTime)
    {
        $selectedDateTime = Carbon::createFromFormat('Y-m-d H:i', $dateTime);     
        foreach ($this->weekBookedSessions as $dayBookedSessions) {
            foreach ($dayBookedSessions as $bookedSession) {
                $start = Carbon::createFromFormat('Y-m-d H:i', $bookedSession);
                $end = $start->copy()->addMinutes($this->sessionDuration);
                if ($selectedDateTime >= $start && $selectedDateTime < $end) {

                    $this->addError('bookingError', 'This session overlaps with an existing booking.');
                    return;
                }
            }
        }
        foreach ($this->weekBookedSessions as &$dayBookedSessions) {
            $dayBookedSessions[] = $selectedDateTime->format('Y-m-d H:i');
            usort($dayBookedSessions, function ($a, $b) {
                return $a <=> $b;
            });
        }
        request()->session()->put('weekBookedSessions', serialize($this->weekBookedSessions));
    }
    public function saveSessionName($dateTime)
    {
        $sessionName = $this->sessionNames[$dateTime] ?? null;
        if ($sessionName) {
            foreach ($this->weekBookedSessions as &$dayBookedSessions) {
                foreach ($dayBookedSessions as &$session) {
                    if ($session === $dateTime) {
                        $session .= " ($sessionName)";
                    }
                }
            }
            $this->emitSelf('sessionNameUpdated');
        }
    }


}
