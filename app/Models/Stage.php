<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pipeline_id',
        'label',
        'display_order',
        'hubspot_id',
        'hubspot_pipeline_id',
        'hubspot_createdAt',
        'hubspot_updatedAt',
        'isClosed',
        'probability'
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

    public function pipeline()
    {
        return $this->belongsTo('App\Models\Pipeline');
    }
}
