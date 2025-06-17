<?php

namespace App\Entity\Sensor\SensorTypes\Interfaces;

interface SensorTypeInterface
{
    public static function getSensorTypeName(): string;

    public static function getAllowedReadingTypes(): array;
}
