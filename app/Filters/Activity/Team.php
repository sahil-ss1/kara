<?php

namespace App\Filters\Activity;

use Illuminate\Database\Eloquent\Builder;
use Pricecurrent\LaravelEloquentFilters\AbstractEloquentFilter;

class Team extends AbstractEloquentFilter
{
    protected $teams=[];

    public function __construct(array $teams)
    {
        $this->teams = $teams;
    }

    public function apply(Builder $query) : Builder
    {
        if ( !empty($this->teams) ) {
            return $query->whereHas('member', function($q) {
                $q->whereHas( 'teams', function ( $q2 ) {
                    $q2->where( 'teams.id', $this->teams );
                });
            });
        }else{
            return $query;
        }
    }
}
