<?php

namespace App\Livewire\Dashboard;

use App\Models\Team;
use Auth;
use Livewire\Component;

class PeriodSelector extends Component
{
    public $periods=[];
    public $selected_period;
    public $member=null;

    public function updatedSelectedPeriod()
    {
        $this->dispatch('dashboard-counters-period-change', ['values' => $this->selected_period]);
    }

    public function render()
    {
        return view('livewire.dashboard.period-selector');
    }
}
