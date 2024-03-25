<?php

namespace App\Sensors\SensorServices\Trigger\TriggerHelpers;

class TriggerDateTimeConvertor
{
    public static function prepareTimesForComparison(?string $time = null): int
    {
        $currentTime = $time ?? date('H:i');
        $currentTime = str_replace([':', ' '], '', $currentTime);

        return (int)$currentTime;

    }

    public static function prepareDaysForComparison(?string $day = null): string
    {
        $currentDay = $day ?? date('l');

        return strtolower($currentDay);
    }
}
