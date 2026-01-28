<?php

namespace App\Helpers;

use Carbon\Carbon;

class Periods {

    public static $type=[
        'lastweek' => 'Last week',
        'lastmonth' => 'Last month',
        'lastquarter' => 'Last quarter',
        'lastyear' => 'Last year',
        'thisweek' => 'This week',
        'thismonth' => 'This month',
        'thisquarter' => 'This quarter',
        'thisyear' => 'This year',
        'alltime' => 'All Time'
    ];

    public static function get($type): array {
        $period=[];
        switch ($type) {
            case 'lastweek':
                $startOfLastWeek = Carbon::now()->subDays(7)->startOfWeek();
                $endOfLastWeek = Carbon::now()->subDays(7)->endOfWeek();
                $period=['from'=> $startOfLastWeek, 'to'=> $endOfLastWeek ];
                break;
            case 'lastmonth':
                $start = new Carbon('first day of last month');
                $end = new Carbon('last day of last month');
                $period=['from'=> $start , 'to'=> $end ];
                break;
            case 'lastquarter':
                $firstOfQuarter = Carbon::now()->subMonths(3)->firstOfQuarter();
                $lastOfQuarter = Carbon::now()->subMonths(3)->lastOfQuarter();
                $period=['from'=> $firstOfQuarter , 'to'=> $lastOfQuarter ];
                break;
            case 'lastyear':
                $date = new Carbon('-1 year');
                $startOfYear = $date->copy()->startOfYear();
                $endOfYear   = $date->copy()->endOfYear();
                $period=['from'=> $startOfYear , 'to'=> $endOfYear ];
                break;
            case 'thisweek':
                $startOfWeek = Carbon::now()->startOfWeek();
                $endOfWeek = Carbon::now()->endOfWeek();
                $period=['from'=> $startOfWeek, 'to'=> $endOfWeek ];
                break;
            case 'thismonth':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $period=['from'=> $start , 'to'=> $end ];
                break;
            case 'thisquarter':
                $firstOfQuarter = Carbon::now()->firstOfQuarter();
                $lastOfQuarter = Carbon::now()->lastOfQuarter();
                $period=['from'=> $firstOfQuarter , 'to'=> $lastOfQuarter ];
                break;
            case 'thisyear':
                $startOfYear = Carbon::now()->startOfYear();
                $endOfYear   = Carbon::now()->endOfYear();
                $period=['from'=> $startOfYear , 'to'=> $endOfYear ];
                break;
            default:
                break;
        }

        return $period;
    }


}
