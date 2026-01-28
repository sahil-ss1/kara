<?php

namespace App\Enum;

enum GoalType : string {
    case DEAL = 'Deal';
    //case ACTIVITY = 'Activity';
    case TASK='Task';
    case CALL='Call';
    case MEETING='Meeting';
    case DEADLINE='Deadline';
    case EMAIL='Email';
    case LUNCH='Lunch';
    case NOTE='Note';
}
