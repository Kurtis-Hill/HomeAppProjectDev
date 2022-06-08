<?php

namespace App\Sensors\SensorServices\NewReadingType;

use App\Sensors\Entity\Sensor;

interface ReadingTypeCreationInterface
{
    public function handleSensorReadingTypeCreation(Sensor $sensor): array;
}
