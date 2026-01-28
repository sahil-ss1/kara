<?php

namespace App\Enum;

use Auth;

enum DealWarning : string {
    case LAST_ACTIVITY = 'LastActivity';
    case CLOSE_DATE = 'CloseDate';
    case STAGE_TIME_SPEND='StageTimeSpend';
    case CREATION_DATE = 'CreationDate';

    public function label(): string
    {
        return match($this)
        {
            self::LAST_ACTIVITY => __('Last activity was too long ago'),
            self::CLOSE_DATE => __('Close date has passed'),
            self::STAGE_TIME_SPEND => __('Time spend in a stage too long'),
            self::CREATION_DATE => __('Creation date was too long ago'),
        };
    }

    public function days(): int {
        $organization = Auth::user()->organization();
        if (!$organization) {
            return 0; // Default to 0 if no organization
        }
        return match($this)
        {
            self::LAST_ACTIVITY => $organization->warn_last_activity_days ?? 0,
            self::CLOSE_DATE => 0,
            self::STAGE_TIME_SPEND => $organization->warn_stage_time_days ?? 0,
            self::CREATION_DATE => $organization->warn_creation_time_days ?? 0,
        };
    }

    public static function forSelect(): array
    {
        $ret=[];
        foreach (self::cases() as $case) {
            $ret[$case->value] = $case->label();
        }
        return $ret;
    }

    /*
    public static function values(): array
    {
        return array_combine(
            array_column(self::cases(), 'value'),
            array_column(self::cases(), 'name')
        );
    }
    */



}
