<?php

namespace App\Services\Sensor\Trigger\TriggerHelpers;

class TriggerDateTimeConvertor
{
    public static function prepareTimes(?string $time = null): int
    {
        $currentTime = $time ?? date('H:i');
        $currentTime = str_replace([':', ' '], '', $currentTime);

        return (int)$currentTime;

    }

    public static function prepareDays(?string $day = null): string
    {
        $currentDay = $day ?? date('l');

        return strtolower($currentDay);
    }
}
