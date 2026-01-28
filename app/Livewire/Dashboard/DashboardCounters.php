<?php

namespace App\Livewire\Dashboard;

use App\Helpers\Periods;
use Livewire\Attributes\On;
use Livewire\Component;

class DashboardCounters extends Component
{
    public $selected_team;
    public $periods;
    public $selected_period;
    public $member=null;

    public function mount(){
        $this->periods = Periods::$type;
    }

    #[On('teams-select-change')]
    public function teamSelection($team){
        if (!is_array($team)) $team=[$team];
        else if(isset($team['values'])) $team = $team['values'];
        $this->selected_team = $team;
        if ($this->member) {
            $this->selected_team = $this->member->teams()->pluck( 'teams.id' )->toArray();
        }
    }

    #[On('dashboard-counters-period-change')]
    public function periodSelection($period){
        $this->selected_period = $period['values'];
    }

    public function updatedSelectedPeriod()
    {
        $this->dispatch('dashboard-counters-period-change', ['values' => $this->selected_period]);
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-counters')->with([
            'member' => $this->member,
        ]);
    }
}
