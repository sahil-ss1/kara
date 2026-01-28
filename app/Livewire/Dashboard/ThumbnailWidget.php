<?php

namespace App\Livewire\Dashboard;

use App\Enum\GoalType;
use App\Helpers\Periods;
use App\Models\Activity;
use App\Models\Member;
use App\Models\Team;
use Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ThumbnailWidget extends Component
{
    public $selected_team=[];
    public $members=null;

    #[On('teams-select-change')]
    public function teamSelection($team){
        if (!is_array($team)) {
            $team = [$team];
        } else if(isset($team['values'])) {
            $team = $team['values'];
            // Ensure it's an array even if values is a string
            if (!is_array($team)) {
                $team = [$team];
            }
        }

        // Ensure selected_team is always an array
        $this->selected_team = is_array($team) ? $team : [$team];
    }

    public function render()
    {
        $organization = Auth::user()->organization();
        if (!$organization) {
            $this->members = collect([]);
            return view('livewire.dashboard.thumbnail-widget');
        }
        // Ensure selected_team is an array for whereIn
        $teamIds = is_array($this->selected_team) ? $this->selected_team : (!empty($this->selected_team) ? [$this->selected_team] : [0]);
        
        $this->members = Member::select('members.*')->where('organization_id', $organization->id)->where('active',1)
            ->join('member_team', 'members.id', '=', 'member_team.member_id')->whereIn('member_team.team_id', $teamIds)
            ->orderBy('member_team.id')->get();

        return view('livewire.dashboard.thumbnail-widget');
    }
}
