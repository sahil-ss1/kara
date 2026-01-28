<?php

namespace App\Livewire\Dashboard;

use App\Helpers\Periods;
use App\Models\Deal;
use App\Models\Member;
use Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class AverageBasketWidget extends Component
{
    public $selected_period;
    public $selected_team;

    public $amount;
    public $count;

    private $startdate=null;
    private $enddate=null;
    public $member=null;

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
        
        if ($this->member) {
            $this->selected_team = $this->member->teams()->pluck( 'teams.id' )->toArray();
        }
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

    public function render()
    {
        $organization = Auth::user()->organization();
        if (!$organization) {
            $this->amount = currency_format(0, Auth::user()->currency());
            return view('livewire.dashboard.average-basket-widget');
        }
        $deals = Deal::whereHas('pipeline', function($q) use ($organization){
            $q->where('organization_id', '=', $organization->id);
            $q->where('active',1);
        })->whereHas('member', function($q){
            $q->whereHas('teams',  function($q2){
                // Ensure selected_team is an array for whereIn
                $teamIds = is_array($this->selected_team) ? $this->selected_team : (!empty($this->selected_team) ? [$this->selected_team] : [0]);
                $q2->whereIn('teams.id', $teamIds);
            });
        })->whereHas('stage', function($q) {
            $q->where('probability', 1);
        });

        if ($this->member)
            $deals->where('member_id', $this->member->id);

        $this->fillPeriodDates();
        if (($this->startdate)&&($this->enddate))
            $deals->whereBetween('closedate', [$this->startdate, $this->enddate]);
        $currency = Auth::user()->currency();

        $members = Member::distinct()->whereHas('teams',  function($q) use ($organization){
                        $q->where('organization_id', '=', $organization->id);
                        // Ensure selected_team is an array for whereIn
                        $teamIds = is_array($this->selected_team) ? $this->selected_team : (!empty($this->selected_team) ? [$this->selected_team] : [0]);
                        $q->whereIn('teams.id', $teamIds);
                    })->whereHas('deals',  function($q){
                        $q->whereHas('pipeline',  function($q2){
                            $q2->where('active',1);
                        });
                        $q->whereHas('stage',  function($q2){
                            $q2->where('probability',1);
                        });
                    });

        if ($this->member)
            $members->where('id', $this->member->id);

        if (!$this->selected_team){
            $this->amount = currency_format(0, $currency);
        }else {
            $this->count = $members->count();
            if ($this->count<>0)
                $this->amount = currency_format($deals->sum('amount') / $this->count, $currency);
            else $this->amount = 0;
        }

        return view('livewire.dashboard.average-basket-widget');
    }
}
