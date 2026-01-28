<?php

namespace App\Filters\Activity;

use Auth;
use Illuminate\Database\Eloquent\Builder;
use Pricecurrent\LaravelEloquentFilters\AbstractEloquentFilter;

class Pipeline extends AbstractEloquentFilter
{
    protected $pipeline=[];

    public function __construct(array $pipeline)
    {
        $this->pipeline = $pipeline;
    }

    public function apply(Builder $query): Builder
    {
        $organization = Auth::user()->organization();
        if (!$organization) {
            return $query->whereRaw('1 = 0'); // Return empty query
        }
        if ( empty($this->pipeline) ) {
            return $query->whereHas('deal', function($q) use ($organization){
                $q->whereHas('pipeline', function($q2) use ($organization){
                    $q2->where('organization_id', '=', $organization->id);
                    $q2->where('active',1);
                });
            });
        }else{
            return $query->whereHas('deal', function($q) use ($organization){
                $q->whereHas('pipeline', function($q2) use ($organization){
                    $q2->where('organization_id', '=', $organization->id);
                    $q2->where('active',1);
                    $q2->whereIn('id', $this->pipeline);
                });
            });
        }
    }
}
