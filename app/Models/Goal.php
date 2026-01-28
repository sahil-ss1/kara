<?php

namespace App\Models;

use App\Enum\GoalActivityType;
use App\Enum\GoalInterval;
use App\Enum\GoalMetric;
use App\Enum\GoalStageStatus;
use App\Enum\GoalType;
use App\Enum\GoalTypeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pipeline_id',
        'stage_id',
        'team_id',
        'name',
        'value',
        'start_date',
        'end_date',
        'metric',
        'type',
        'type_status',
        'activity_type',
        'stage_status',
        'interval',
        'active',
        'member_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'metric' => GoalMetric::class,
        'type' => GoalType::class,
        'type_status' => GoalTypeStatus::class,
        'activity_type' => GoalActivityType::class,
        'stage_status' => GoalStageStatus::class,
        'interval' => GoalInterval::class,
    ];

    public function pipeline()
    {
        return $this->belongsTo('App\Models\Pipeline');
    }

    public function stage()
    {
        return $this->belongsTo('App\Models\Stage');
    }

    public function team()
    {
        return $this->belongsTo('App\Models\Team');
    }

    public function owner()
    {
        return $this->belongsTo('App\Models\Member');
    }
}
