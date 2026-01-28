<?php

namespace App\Filters\Deal;

use Auth;
use Illuminate\Database\Eloquent\Builder;
use Pricecurrent\LaravelEloquentFilters\AbstractEloquentFilter;

class Pipeline extends AbstractEloquentFilter
{
    protected $pipeline=[];

    public function __construct(array $pipeline)
    {
        foreach ($pipeline as $item)
            if (!empty($item)) $this->pipeline[] = $item;
    }

    public function apply(Builder $query): Builder
    {
        $organization = Auth::user()->organization();
        if (!$organization) {
            return $query->whereRaw('1 = 0'); // Return empty query
        }
        if ( empty($this->pipeline) ) {
            return $query->whereHas('pipeline', function($q) use ($organization){
                $q->where('organization_id', '=', $organization->id);
                $q->where('active',1);
            });
        }else{
            return $query->whereHas('pipeline', function($q) use ($organization){
                $q->where('organization_id', '=', $organization->id);
                $q->where('active',1);
                $q->whereIn('id', $this->pipeline);
            });
        }
    }

    public function getDatatableFilter(){
        if ( !empty($this->pipeline) ) {
            return 'd.pipelines='.implode(',',$this->pipeline);
        }else return '';
    }
}
