<?php

namespace App\Filters\Deal;

use App\Enum\GoalInterval;
use App\Enum\GoalTypeStatus;
use App\Helpers\Periods;
use Illuminate\Database\Eloquent\Builder;
use Pricecurrent\LaravelEloquentFilters\AbstractEloquentFilter;

class Interval extends AbstractEloquentFilter
{
    protected $interval;
    protected GoalTypeStatus $status;

    public function __construct(GoalTypeStatus $status , $interval)
    {
        $this->interval= $interval;
        $this->status = $status;
    }

    private function getField() : string {
        return match ( $this->status ) {
            GoalTypeStatus::WON, GoalTypeStatus::LOST, GoalTypeStatus::CLOSED => 'closedate',
            default => 'createdate',
        };
    }

    private function getPeriod() : array {
        return match ( $this->interval ) {
            GoalInterval::WEEK => Periods::get( 'thisweek' ),
            GoalInterval::MONTH => Periods::get( 'thismonth' ),
            GoalInterval::QUARTER => Periods::get( 'thisquarter' ),
            GoalInterval::YEAR => Periods::get( 'thisyear' ),
            default => Periods::get( $this->interval ),
        };

    }

    public function apply(Builder $query) : Builder
    {
        $dates=$this->getPeriod();
        if (!empty($dates))
            return $query->whereBetween($this->getField(), [$dates['from'], $dates['to']]);
        else
            return $query;
    }

    public function getDatatableFilter(){
        $period = match ( $this->interval ) {
            GoalInterval::WEEK => 'thisweek',
            GoalInterval::MONTH => 'thismonth',
            GoalInterval::QUARTER => 'thisquarter',
            GoalInterval::YEAR => 'thisyear',
            default => '',
        };

        return 'd.'.$this->getField().'='.$period;
    }
}
