<?php

namespace App\Models;

use App\Enum\MeetingFeeling;
use App\Enum\MeetingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'manager_id',
        'target_id',
        'title',
        'startAt',
        'endAt',
        'feeling',
        'status',
        'feeling_note',
        'manager_note',
        'target_note',
        'google_event_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'startAt' => 'datetime',
        'endAt' => 'datetime',
        'feeling' => MeetingFeeling::class,
        'status' => MeetingStatus::class,
    ];

    public function manager()
    {
        return $this->belongsTo('App\Models\Member', 'manager_id');
    }

    public function target()
    {
        return $this->belongsTo('App\Models\Member', 'target_id');
    }

    public function todo(){
        return $this->hasMany('App\Models\Todo');
    }
}
