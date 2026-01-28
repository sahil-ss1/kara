<?php

namespace App\Livewire\Goals;

use App\Enum\GoalMetric;
use App\Enum\GoalType;
use App\Models\Activity;
use App\Models\Deal;
use App\Models\Member;
use App\Models\Pipeline;
use App\Models\Stage;
use Auth;
use Livewire\Component;
use Pricecurrent\LaravelEloquentFilters\EloquentFilters;

class GoalDashboardItem extends Component
{
    public $goal;
    private $filters=[];
    public $goal_team=null;
    public $goal_member=null;

    private function members(){
        $organization = Auth::user()->organization();
        if (!$organization) {
            return collect([]);
        }
        return Member::select('members.*')->where('organization_id', $organization->id)->where('active',1)
                     ->join('member_team', 'members.id','=', 'member_team.member_id')->whereIn('team_id', $this->goal['team_id'])
                     ->get();
    }

    private function getQuery(){
        if ($this->goal['type'] == GoalType::DEAL) {
            $filters = EloquentFilters::make([
                new \App\Filters\Deal\Pipeline([$this->goal['pipeline_id']]),
                new \App\Filters\Deal\Team([$this->goal['team_id']]),
                new \App\Filters\Deal\TypeStatus($this->goal['type_status']),
                new \App\Filters\Deal\Interval($this->goal['type_status'], $this->goal['interval']),
            ]);
            $query = Deal::filter($filters);
        }else{
            $filters = EloquentFilters::make([
                new \App\Filters\Activity\Pipeline([]),
                new \App\Filters\Activity\Team([$this->goal['team_id']]),
                new \App\Filters\Activity\TypeStatus($this->goal['type_status']),
                new \App\Filters\Activity\Interval($this->goal['type_status'], $this->goal['interval']),
            ]);
            $query = Activity::filter($filters);
        }
        $this->filters = $filters;

        if($this->goal->member_id)
            $query->where('member_id', $this->goal->member_id);

        return $query;
    }

    private function getDatatableFilters(){
        $filters='';
        foreach ( $this->filters as $filter ){
            if ( method_exists($filter,'getDatatableFilter') )
                $filters.=$filter->getDatatableFilter()."\n";
        }

        return $filters;
    }

    private function getDatatableFiltersAsArray():array{
        $filters=[];
        foreach ( $this->filters as $filter ){
            if ( method_exists($filter,'getDatatableFilter') )
                $filters[]=$filter->getDatatableFilter();
        }

        return $filters;
    }

    public function render()
    {
        $value=0;$target=$this->goal['value'];
        if ($this->goal['type'] == GoalType::DEAL) {
            $deals = $this->getQuery();
            if ($this->goal['metric'] == GoalMetric::VALUE)
                $value = $deals->sum('amount');
            else
                $value = $deals->count();

        }else{
            $activities = $this->getQuery();

            $value = $activities->count();
        }
        if ($target!=0) $percentage =  ($value / $target) * 100; else $percentage = 0;
        $currency = Auth::user()->currency();
        if ($this->goal['metric'] == GoalMetric::VALUE) $value=currency_format($value, $currency);

        $member = null;
        if ($this->goal->member_id) {
            $member = Member::select('members.*')
                ->where('id', $this->goal->member_id);
            $member = $member->get()->first();
        }

        $organization = Auth::user()->organization();
        if (!$organization) {
            $pipelines = collect([]);
        } else {
            $pipelines = Pipeline::where('organization_id', $organization->id)->where('active', '1')->get();
        }
        $stages = collect();
        if ($this->goal->pipeline_id) $stages = Stage::where('pipeline_id', $this->goal->pipeline_id)->get();

        return view('livewire.goals.goal-dashboard-item')->with([
            'target' => ($this->goal['metric'] == GoalMetric::VALUE ? currency_format($target, $currency) : $target ),
            'value' => $value,
            'percentage' => $percentage,
            'pipelines' => $pipelines,
            'stages' => $stages,
            'member' => $member,
            'filters' => json_encode($this->getDatatableFiltersAsArray()),
            'goal_team' => $this->goal_team,
            'goal_member' => $this->goal_member
            //'amount' => currency_format($amount, $currency)
        ]);
    }
}
