<?php

namespace App\Livewire\Dashboard;

use App\Helpers\Periods;
use App\Models\Deal;
use Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\TeamMember;

class DealsLostWidget extends Component
{
    public $selected_period;
    public $selected_team;

    public $amount;
    public $count;

    private $startdate=null;
    private $enddate=null;
    public $member=null;
    public $team_id=null;

    #[On('teams-select-change')]
    public function teamSelection($team){
        $this->team_id = $team['values'][0] ?? null;
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

    #[On('dashboard-counters-period-change')]
    public function periodSelection($period){
        $this->selected_period = $period['values'];
    }

    #[On('refresh-counter')]
    public function refresh()
    {
        //
    }

    private function fillPeriodDates(){
        $dates=Periods::get($this->selected_period);
        if ($dates) {
            $this->startdate= $dates['from'];
            $this->enddate= $dates['to'];
        }else {
            $this->startdate=null;
            $this->enddate=null;
        }
    }

    private function getQuery() {      
        $organization = Auth::user()->organization();
        if (!$organization) {
            return Deal::whereRaw('1 = 0'); // Return empty query
        }
        $deals = Deal::whereHas( 'pipeline', function ( $q ) use ($organization) {
            $q->where( 'organization_id', '=', $organization->id );
            $q->where( 'active', 1 );
        } )->whereHas( 'stage', function ( $q ) {
            $q->where( 'probability', 0 );
        } );
        if($this->team_id && $this->team_id != 'None' && $this->team_id != 'N'){
                $deals->whereHas('member', function($q){
                    $q->whereHas('teams',  function($q2){
                        // Ensure selected_team is an array for whereIn
                        $teamIds = is_array($this->selected_team) ? $this->selected_team : (!empty($this->selected_team) ? [$this->selected_team] : [0]);
                        $q2->whereIn('teams.id', $teamIds);
                    });
                });
        }
        if ($this->member)
            $deals->where('member_id', $this->member->id);

        $this->fillPeriodDates();
        if ( ( $this->startdate ) && ( $this->enddate ) ) {
            $deals->whereBetween( 'closedate', [ $this->startdate, $this->enddate ] );
        }
        return $deals;
    }

    public function render()
    {
        //ray()->showQueries();
        $currency = Auth::user()->currency();

        // if (!$this->selected_team){
        //     $this->count = 0;
        //     $this->amount = currency_format(0, $currency);
        // }else {
            $deals = $this->getQuery();
            $this->count = $deals->count();
            $this->amount = currency_formatter($deals->sum('amount'), $currency);
        // }
       

        return view('livewire.dashboard.deals-lost-widget');
    }
}
