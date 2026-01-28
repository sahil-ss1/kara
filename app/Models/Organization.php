<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

class Organization extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'currency',
        'synchronizing',
        'last_sync',
        'timezone',
        'hubspot_uiDomain',
        'hubspot_portalId',
        'warn_last_activity_days',
        'warn_stage_time_days',
        'warn_creation_time_days'
    ];

    protected $casts = [
        'last_sync' => 'datetime',
        'synchronizing' => 'boolean'
    ];

    public function users(){
         return $this->belongsToMany('App\Models\User');
    }

    public function isSynchronizing() : Bool {
        return $this->synchronizing;
    }

    public function isSynchronizing2() : Bool {
        return Organization::find($this->id)->synchronizing;
    }

    public function getDayFromLastSync(){
        if ($this->last_sync){
            return $this->last_sync->diffInDays(now());
        }else return null;
    }

    public function getHubspotURL(){
        return 'https://'.$this->hubspot_uiDomain .'/contacts/' . $this->hubspot_portalId;
    }
}
