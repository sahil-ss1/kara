<?php

namespace App\Filters\Deal;

use App\Enum\GoalStageStatus;
use Illuminate\Database\Eloquent\Builder;
use Pricecurrent\LaravelEloquentFilters\AbstractEloquentFilter;

class Stage extends AbstractEloquentFilter
{
    protected $stage_id;
    protected GoalStageStatus $stage_status;

    public function __construct(GoalStageStatus $stage_status, $stage_id)
    {
        $this->stage_status = $stage_status;
        $this->stage_id = $stage_id;
    }

    public function apply(Builder $query) : Builder
    {
        if ( $this->stage_id && in_array($this->stage_status, [GoalStageStatus::MOVED_IN, GoalStageStatus::IN_STAGE]) ){
            return $query->whereHas('stage', function($q) {
                $q->where('id', $this->stage_id);
            });
        }else return $query;
    }

    public function getDatatableFilter(){
        if ( $this->stage_id && in_array($this->stage_status, [GoalStageStatus::MOVED_IN, GoalStageStatus::IN_STAGE]) ){
            return 'd.stage='.$this->stage_id;
        }else return '';
    }
}
