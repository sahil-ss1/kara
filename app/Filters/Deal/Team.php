<?php

namespace App\Filters\Deal;

use Illuminate\Database\Eloquent\Builder;
use Pricecurrent\LaravelEloquentFilters\AbstractEloquentFilter;

class Team extends AbstractEloquentFilter
{

    protected $teams=[];

    public function __construct(array $teams)
    {
        foreach ($teams as $item)
            if (!empty($item)) $this->teams[] = $item;
    }

    public function apply(Builder $query) : Builder
    {
        if ( !empty($this->teams) ) {
            return $query->whereHas('member', function($q) {
                        $q->whereHas( 'teams', function ( $q2 ) {
                            $q2->whereIn( 'teams.id', $this->teams );
                    });
            });
        }else{
            return $query;
        }
    }

    public function getDatatableFilter(){
        if ( !empty($this->teams) ) {
            return 'd.teams=['.implode(',',$this->teams).']';
        }else return '';
    }
}
