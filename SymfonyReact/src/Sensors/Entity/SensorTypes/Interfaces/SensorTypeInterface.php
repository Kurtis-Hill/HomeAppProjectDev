<?php

namespace App\Sensors\Entity\SensorTypes\Interfaces;

use App\Sensors\Entity\Sensor;

interface SensorTypeInterface
{
    public function setSensor(Sensor $sensor);

    public function getSensorTypeID(): int;

    public function getSensor(): Sensor;

    // make sure this returns the same data as in the seensortype table in the sensorType column
    public function getReadingTypeName(): string;

    public static function getReadingTypeAlias(): string;

    public static function getAllowedReadingTypes(): array;
}
