<?php

namespace App\Enum;

enum GoalInterval : string {
    case WEEK='Week';
    case MONTH='Month';
    case QUARTER='Quarter';
    case YEAR='Year';
    case ONCE='Once';

    public function label(): string
    {
        return match($this)
        {
            self::WEEK => __('every week'),
            self::MONTH => __('every month'),
            self::QUARTER => __('every quarter'),
            self::YEAR => __('every year'),
            self::ONCE => __('only once'),
        };
    }
}
