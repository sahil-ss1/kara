<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $table = 'member_team';

    protected $fillable = [
        'team_id',
        'member_id',
    ];
}
