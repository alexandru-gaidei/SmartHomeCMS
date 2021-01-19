<?php

namespace App\Traits;

trait SensorMeta
{
    public static $SRC_TYPES = [
        self::SRC_TYPE_FETCH => 'Fetch',
        self::SRC_TYPE_PUSH => 'Push',
    ];

    public static $VAL_TYPES = [
        self::VAL_TYPE_BOOL => 'Switch (on/off)',
        self::VAL_TYPE_NUM => 'Number',
    ];

    public static $RRULE_FREQ = [
        'MINUTELY' => 'Minute',
        'HOURLY' => 'Hour',
        'DAILY' => 'Day',
        'WEEKLY' => 'Week',
        'MONTHLY' => 'Month',
        'YEARLY' => 'Year',
    ];

    public static $RRULE_INTERVAL = [
        1 => 1,
        2 => 2,
        3 => 3,
        5 => 5,
        10 => 10,
        15 => 15,
        30 => 30,
    ];
}
