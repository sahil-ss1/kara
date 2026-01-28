<?php

namespace App\Enum;

enum MeetingStatus : String {
    case ACTIVE='Active';
    case STARTED='Started';
    case VALIDATED='Validated';
    case DONE='Done';
    case CANCELLED='Cancelled';
}
