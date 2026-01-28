<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'organization_id'
    ];

    public function organization()
    {
        return $this->belongsTo('App\Models\Organization');
    }

    public function members()
    {
        return $this->belongsToMany('App\Models\Member');
    }

    public function activeMembers()
    {
        return $this->belongsToMany('App\Models\Member')->where('active', 1);
    }

    public function goals()
    {
        return $this->hasMany('App\Models\Goal');
    }
}
