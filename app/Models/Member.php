<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization_id',
        'hubspot_id',
        'hubspot_createdAt',
        'hubspot_updatedAt',
        'email',
        'firstName',
        'lastName',
        'active',
        'hubspot_archived'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hubspot_createdAt' => 'datetime',
        'hubspot_updatedAt' => 'datetime',
    ];

    protected static function booted(){
        self::creating(function($member){
            $member->active=!$member->hubspot_archived;
        });
    }

    //Accessor full_name
    public function getFullNameAttribute()
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    /**
     * Get the member's initials safely.
     * Prevents "Uninitialized string offset" errors if names are empty.
     */
    public function getInitialsAttribute(): string
    {
        $first = mb_substr($this->firstName ?? '', 0, 1);
        $last = mb_substr($this->lastName ?? '', 0, 1);
        return $first . $last;
    }

    public function organization()
    {
        return $this->belongsTo('App\Models\Organization');
    }

    function deals(){
        return $this->hasMany('App\Models\Deal');
    }

    public function teams()
    {
        return $this->belongsToMany('App\Models\Team');
    }

    public function meetingsAsManager()
    {
        return $this->hasMany('App\Models\Meetings', 'manager_id', 'id');
    }

    public function meetingsAsTarget()
    {
        return $this->hasMany('App\Models\Meetings', 'target_id', 'id');
    }

    public function goals(){
        return $this->hasMany('App\Models\Goal');
    }

    public function openMeet(){
        $userMember = Auth::user()->member()->first();
        if (!$userMember) {
            // Return empty query if user doesn't have a corresponding member
            return Meeting::whereRaw('1 = 0');
        }
        
        return Meeting::where('status', \App\Enum\MeetingStatus::ACTIVE->value)
                        ->where(function ($q) use ($userMember) {
                            $q->where(function($q2) use ($userMember) {
                                $q2->where('manager_id', $this->id);
                                $q2->where('target_id', $userMember->id);
                            })->orWhere(function($q2) use ($userMember) {
                                $q2->where('manager_id', $userMember->id);
                                $q2->where('target_id', $this->id);
                            });
                        });
    }


}
