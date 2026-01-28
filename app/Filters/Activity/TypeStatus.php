<?php

namespace App\Filters\Activity;

use App\Enum\GoalTypeStatus;
use Illuminate\Database\Eloquent\Builder;
use Pricecurrent\LaravelEloquentFilters\AbstractEloquentFilter;

class TypeStatus extends AbstractEloquentFilter
{
    protected GoalTypeStatus $status;

    public function __construct(GoalTypeStatus $status)
    {
        $this->status = $status;
    }

    public function apply(Builder $query) : Builder
    {
       if ($this->status == GoalTypeStatus::IN_PROGRESS)
            return $query;
        else if ($this->status == GoalTypeStatus::CLOSED)
            return $query->where('hubspot_status', 'COMPLETED');
        else return $query;
    }
}
