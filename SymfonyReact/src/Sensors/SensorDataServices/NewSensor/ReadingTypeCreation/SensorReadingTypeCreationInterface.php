<?php

namespace App\Sensors\SensorDataServices\NewSensor\ReadingTypeCreation;

use App\Sensors\Entity\Sensor;

interface SensorReadingTypeCreationInterface
{
    public function handleSensorReadingTypeCreation(Sensor $sensor): array;
}
