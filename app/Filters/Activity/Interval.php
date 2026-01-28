<?php

namespace App\Filters\Activity;

use App\Enum\GoalInterval;
use App\Enum\GoalTypeStatus;
use App\Helpers\Periods;
use Illuminate\Database\Eloquent\Builder;
use Pricecurrent\LaravelEloquentFilters\AbstractEloquentFilter;

class Interval extends AbstractEloquentFilter
{
    protected GoalInterval $interval;
    protected GoalTypeStatus $status;

    public function __construct(GoalTypeStatus $status , GoalInterval $interval)
    {
        $this->interval= $interval;
        $this->status = $status;
    }

    private function getField() : string {
        return match ( $this->status ) {
            GoalTypeStatus::CREATED,
            GoalTypeStatus::IN_PROGRESS,
            GoalTypeStatus::CLOSED => 'hubspot_timestamp',
            default => '',
        };
    }

    private function getPeriod() : array {
        return match ( $this->interval ) {
            GoalInterval::WEEK => Periods::get( 'thisweek' ),
            GoalInterval::MONTH => Periods::get( 'thismonth' ),
            GoalInterval::QUARTER => Periods::get( 'thisquarter' ),
            GoalInterval::YEAR => Periods::get( 'thisyear' ),
            default => [],
        };
    }

    public function apply(Builder $query) : Builder
    {
        $dates=$this->getPeriod();
        if ($this->getField() && !empty($dates)){
            return $query->whereBetween($this->getField(), [$dates['from'], $dates['to']]);
        }else return $query;

    }
}
