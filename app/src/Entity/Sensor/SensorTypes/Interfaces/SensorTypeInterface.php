<?php

namespace App\Entity\Sensor\SensorTypes\Interfaces;

interface SensorTypeInterface
{
    public static function getReadingTypeName(): string;

    public static function getAllowedReadingTypes(): array;
}
