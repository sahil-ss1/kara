<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pricecurrent\LaravelEloquentFilters\Filterable;

class Deal extends Model
{
    use HasFactory, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pipeline_id',
        'stage_id',
        'member_id',
        'hubspot_id',
        'hubspot_createdAt',
        'hubspot_updatedAt',
        'name',
        'amount',
        'closedate',
        'createdate',
        'hubspot_pipeline_id',
        'hubspot_stage_id',
        'hubspot_owner_id',
        'hs_date_entered',
        'hs_is_closed',
        'hs_is_closed_won',
        'kara_probability',
        'hs_next_step',
        'hs_manual_forecast_category',
        //'total_tasks',
        //'total_calls',
        //'total_emails',
        //'total_meetings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hubspot_createdAt' => 'datetime',
        'hubspot_updatedAt' => 'datetime',
        'closedate' => 'datetime',
        'createdate' => 'datetime',
        'hs_date_entered' => 'datetime',
        'hs_is_closed_won' => 'boolean',
        'hs_is_closed' => 'boolean',
    ];

    public function pipeline()
    {
        return $this->belongsTo('App\Models\Pipeline');
    }

    public function stage()
    {
        return $this->belongsTo('App\Models\Stage');
    }

    public function member()
    {
        return $this->belongsTo('App\Models\Member');
    }
}
