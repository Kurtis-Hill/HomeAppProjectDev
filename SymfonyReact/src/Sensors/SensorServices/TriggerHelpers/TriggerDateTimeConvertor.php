<?php

namespace App\Sensors\SensorServices\TriggerHelpers;

class TriggerDateTimeConvertor
{
    public static function prepareTimesForComparison(?string $time): int
    {
        $currentTime = $time ?? date('H:i');
        $currentTime = str_replace([':', ' '], '', $currentTime);
        return (int)$currentTime;

    }

    public static function prepareDaysForComparison(?string $day): string
    {
        $currentDay = $day ?? date('l');
        return lcfirst($currentDay);
    }
}
