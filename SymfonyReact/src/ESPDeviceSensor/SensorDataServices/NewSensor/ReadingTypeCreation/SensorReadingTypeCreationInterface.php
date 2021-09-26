<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor\ReadingTypeCreation;

use App\Entity\Sensors\Sensors;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;

interface SensorReadingTypeCreationInterface
{
    public function handleSensorReadingTypeCreation(Sensors $sensor): void;
}
