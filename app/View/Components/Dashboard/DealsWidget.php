<?php

namespace App\View\Components\Dashboard;

use App\Models\Pipeline;
use Auth;
use Illuminate\View\Component;

class DealsWidget extends Component
{

    public string $teams = 'new Array(($("#teams-select").val())&&new Array($("#teams-select").val()).length>0)?new Array($("#teams-select").val()):[0]';
    public string $members = '$("#deals-members-select").val()?[$("#deals-members-select").val()]:[]';

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($members=null, $teams=null)
    {
        if ($members) {
            $this->members = $members;
            $this->teams = '';
        }
        if ($teams) $this->teams = $teams;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $organization = Auth::user()->organization();
        if (!$organization) {
            $pipelines = collect([]);
        } else {
            $pipelines = Pipeline::where('active',1)->where('organization_id', $organization->id)->pluck('label', 'id');
        }
        //$members = Member::where('active','1')->whereHas('teams', function($q){
        //    $q->whereIn('teams.id', array_keys($this->teams->toArray()));
        //})->pluck('lastName', 'id');
        //$teams = Team::where('organization_id', Auth::user()->organization()->id)->pluck('name', 'id');
        return view('components.dashboard.deals-widget')->with([
            'pipelines'=>$pipelines,
            //'teams' => $teams
            //'members' =>$members
        ]);
    }
}
