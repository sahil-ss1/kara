<?php

namespace App\Filters\Deal;

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
        if ($this->status == GoalTypeStatus::WON)
            return $query->whereHas('stage', function($q) {
                $q->where('probability', 1);
            });
        else if ($this->status == GoalTypeStatus::LOST)
            return $query->whereHas('stage', function($q) {
                $q->where('probability', 0);
            });
        else if ($this->status == GoalTypeStatus::IN_PROGRESS)
            return $query->whereHas('stage', function($q) {
                $q->whereBetween('probability', [0.01, 0.99]);
            });
        else if ($this->status == GoalTypeStatus::CLOSED)
            return $query->whereHas('stage', function($q) {
                $q->where('isClosed', 1);
            });
        else if ($this->status == GoalTypeStatus::OPEN)
            return $query->whereHas('stage', function($q) {
                $q->where('isClosed', 0);
            });
        else return $query;
    }

    public function getDatatableFilter(){
        if ($this->status == GoalTypeStatus::WON) {
            return 'd.probability=1';
        }else if ($this->status == GoalTypeStatus::LOST) {
            return 'd.probability=0';
        }else if ($this->status == GoalTypeStatus::IN_PROGRESS) {
            return 'd.probability=[0.01, 0.99]';
        }else if ($this->status == GoalTypeStatus::CLOSED) {
            return 'd.isClosed=1';
        }else if ($this->status == GoalTypeStatus::OPEN) {
            return 'd.isClosed=0';
        }else return '';
    }
}
