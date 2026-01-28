<?php

namespace App\Livewire\Goals;

use App\Enum\GoalMetric;
use App\Enum\GoalType;
use App\Enum\GoalTypeStatus;
use App\Models\Member;
use App\Models\Stage;
use App\Models\Pipeline;
use Auth;
use Livewire\Component;

class GoalItem extends Component
{
    public $goal;
    public $new_goal_id;

    protected $rules = [
        'goal.metric' => 'required',
        'goal.type' => 'required',
        'goal.type_status' => '',
        'goal.interval' => 'required',
        'goal.value' => 'required',
        'goal.pipeline_id' => 'nullable'
    ];

    public function render()
    {
        $organization = Auth::user()->organization();
        if (!$organization) {
            $pipelines = collect([]);
            $stages = collect([]);
            $team_members = [];
        } else {
            $pipelines = Pipeline::where('organization_id', $organization->id)->where('active', '1')->get();
        $stages = collect();
        if ($this->goal->pipeline_id) $stages = Stage::where('pipeline_id', $this->goal->pipeline_id)->get();
        $team_members=[];
        if ($this->goal->team_id) {
            $members = Member::select('members.*')
                                 ->where('organization_id', $organization->id)
                             ->where('active', 1)
                             ->join('member_team', 'members.id','=', 'member_team.member_id')->where('team_id',$this->goal->team_id );
            $members = $members->get();
            foreach ($members as $member){
                $team_members[$member->id] = $member->lastName.' '.$member->firstName;
                }
            }
        }

        $new_goal = $this->new_goal_id == $this->goal->id;

        return view('livewire.goals.goal-item')->with([
            'pipelines' =>  $pipelines,
            'stages' => $stages,
            'members' => $team_members,
            'new_goal' => $new_goal
        ]);
    }
}
