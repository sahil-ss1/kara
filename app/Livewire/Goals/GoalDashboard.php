<?php

namespace App\Livewire\Goals;

use App\Models\Goal;
use Livewire\Attributes\On;
use Livewire\Component;

class GoalDashboard extends Component
{
    public $selected_team;

    public $member=null;
    public $goal_team=null;
    public $goal_member=null;

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

    #[On('refresh-dashboard')]
    public function refresh()
    {
        //
    }

    public function render()
    {
        if($this->member) {
            $this->selected_team = $this->member->teams()->pluck( 'teams.id' )->toArray();
            // Ensure selected_team is an array for whereIn
            $teamIds = is_array($this->selected_team) ? $this->selected_team : (!empty($this->selected_team) ? [$this->selected_team] : [0]);
            $goals = Goal::whereIn('team_id', $teamIds)->where('member_id', $this->member->id)->get();
        } else {
            // Ensure selected_team is an array for whereIn
            $teamIds = is_array($this->selected_team) ? $this->selected_team : (!empty($this->selected_team) ? [$this->selected_team] : [0]);
            $goals = Goal::whereIn('team_id', $teamIds)->where('member_id', null)->get();
        }

        return view('livewire.goals.goal-dashboard')->with([
            'goals' => $goals,
            'goal_team' => $this->goal_team,
            'goal_member' => $this->goal_member
        ]);
    }
}
