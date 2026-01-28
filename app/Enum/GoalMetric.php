<?php

namespace App\Enum;

enum GoalMetric : string {
    case VALUE = 'Value';
    case COUNT = 'Count';

    public function label(): string
    {
        return match($this)
        {
            self::VALUE => __('the value'),
            self::COUNT => __('the quantity'),
        };
    }
}
