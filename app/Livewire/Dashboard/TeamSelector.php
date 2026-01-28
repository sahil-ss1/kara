<?php

namespace App\Livewire\Dashboard;

use App\Models\Team;
use Auth;
use Livewire\Component;

class TeamSelector extends Component
{
    public $teams=[];
    public $selected_team;

    public function updatedSelectedTeam()
    {
        $this->dispatch('teams-select-change', ['values' => $this->selected_team]);
    }

    public function render()
    {
        return view('livewire.dashboard.team-selector');
    }
}
