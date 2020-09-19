<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Week extends Model
{  
    public static function getWeekRange()
    {
        $day = date('w');
        $week_start = date('Y-m-d 00:00:00', strtotime('monday this week'));
        $week_end = date('Y-m-d 23:59:59', strtotime('monday this week') + 86400 * 6);

        return [$week_start, $week_end];
    }
}