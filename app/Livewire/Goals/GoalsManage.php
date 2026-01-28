<?php

namespace App\Livewire\Goals;

use App\Enum\GoalInterval;
use App\Enum\GoalMetric;
use App\Enum\GoalStageStatus;
use App\Enum\GoalType;
use App\Enum\GoalTypeStatus;
use App\Models\Goal;
use App\Models\Member;
use App\Models\Team;
use Auth;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;

class GoalsManage extends Component
{
    public $teams;
    public $selected_team="";
    public $goals=[];
    public $member_id;
    public $team;
    public $member=null;
    public $new_goal_id=null;

    public function mount(){
        $organization = Auth::user()->organization();
        if ($organization) {
            $this->teams = Team::where('organization_id', $organization->id)->pluck('name','id');
        } else {
            $this->teams = [];
        }
    }

    #[On('teams-select-change')]
    public function teamSelection($team){
        if (!is_array($team)) $team=[$team];
        else if(isset($team['values'])) $team = $team['values'];

        $this->selected_team = $team;
    }

    public function deleteGoal(Goal $goal){
        $goal->delete();
    }

    public function add_personal_goal(){
        $goal = Goal::create([
            'member_id' => $this->member_id,
            'team_id' => $this->selected_team,
            'name' => 'New',
            'type' => GoalType::DEAL,
            'type_status' => GoalTypeStatus::CREATED,
            'metric' => GoalMetric::VALUE,
            'interval' => GoalInterval::MONTH,
            'start_date' => Carbon::now(),
            'value' => 0,
            'active' => true
        ]);
        $this->new_goal_id = $goal->id;
    }

    public function add_team_goal(){
        $goal = Goal::create([
            'team_id' => $this->team,
            'name' => 'New',
            'type' => GoalType::DEAL,
            'type_status' => GoalTypeStatus::CREATED,
            'metric' => GoalMetric::VALUE,
            'interval' => GoalInterval::MONTH,
            'start_date' => Carbon::now(),
            'value' => 0,
            'active' => true
        ]);
        $this->new_goal_id = $goal->id;
    }

    public function render()
    {
        if ($this->team && $this->member_id && $this->member_id != 'null') {
            $this->member = Member::find($this->member_id);
            if($this->team == 'null') {
                $this->goals = Goal::all()->where('member_id', $this->member_id)->where('team_id', $this->selected_team);
                $this->teams = $this->member->teams()->orderBy('teams.id')->pluck( 'teams.name','teams.id' )->toArray();
            } else {
                $this->teams = $this->member->teams()->orderBy('teams.id')->pluck( 'teams.name','teams.id' )->toArray();
                if ($this->selected_team) $this->goals = Team::find($this->selected_team)->goals()->where('goals.member_id', null)->get();
            }
        } else {
            if ($this->team && $this->team != 'null') $this->selected_team = $this->team;
            if ($this->selected_team) $this->goals = Team::find($this->selected_team)->goals()->where('goals.member_id', null)->get();
        }
        return view('livewire.goals.goals-manage');
    }
}
