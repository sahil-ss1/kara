<?php

namespace App\Models;

use App\Enum\GoalType;
use App\Enum\TaskPriority;
use App\Enum\TaskType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Pricecurrent\LaravelEloquentFilters\Filterable;

class Activity extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'deal_id',
        'member_id',
        'hubspot_id',
        'type',
        'hubspot_owner_id',
        'hubspot_deal_id',
        'hubspot_createdAt',
        'hubspot_updatedAt',
        'hubspot_status',
        'hubspot_timestamp',
        'hubspot_task_completion_date',
        'hubspot_task_subject',
        'hubspot_task_body',
        'hubspot_task_type',
        'hubspot_task_priority',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hubspot_createdAt' => 'datetime',
        'hubspot_updatedAt' => 'datetime',
        'hubspot_timestamp' => 'datetime',
        'hubspot_task_completion_date' => 'datetime',
        'type' => GoalType::class,
        'hubspot_task_type' => TaskType::class,
        'hubspot_task_priority' => TaskPriority::class
    ];

    public function deal()
    {
        return $this->belongsTo('App\Models\Deal');
    }

    public function member()
    {
        return $this->belongsTo('App\Models\Member');
    }
}
