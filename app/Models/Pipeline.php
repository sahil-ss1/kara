<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pipeline extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization_id',
        'label',
        'hubspot_id',
        'hubspot_createdAt',
        'hubspot_updatedAt',
        'active'
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

    public function organization()
    {
        return $this->belongsTo('App\Models\Organization');
    }

    function stages(){
        return $this->hasMany('App\Models\Stage');
    }

}
