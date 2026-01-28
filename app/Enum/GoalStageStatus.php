<?php

namespace App\Enum;

enum GoalStageStatus : String {
    case MOVED_IN='MovedIn';
    case MOVED_OUT='MovedOut';
    case IN_STAGE='InStage';
    case NONE='None';
}
