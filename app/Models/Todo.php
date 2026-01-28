<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'meeting_id',
        'note',
        'done',
        'due_date'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'due_date' => 'datetime'
    ];

    public function meeting(){
        return $this->belongsTo('App\Models\Meeting');
    }
}
