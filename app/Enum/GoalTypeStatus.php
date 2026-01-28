<?php

namespace App\Enum;

enum GoalTypeStatus : string {
    case CREATED = 'Created';
    case WON = 'Won';
    case LOST = 'Lost';
    case IN_PROGRESS = 'InProgress';
    case OPEN = 'Open';
    case CLOSED = 'Closed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
