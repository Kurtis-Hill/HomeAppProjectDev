<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor\ReadingTypeCreation;

use App\ESPDeviceSensor\Entity\Sensor;

interface SensorReadingTypeCreationInterface
{
    public function handleSensorReadingTypeCreation(Sensor $sensor): array;
}
