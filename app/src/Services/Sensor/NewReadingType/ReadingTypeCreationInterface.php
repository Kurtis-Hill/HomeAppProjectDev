<?php

namespace App\Services\Sensor\NewReadingType;

use App\Entity\Sensor\Sensor;

interface ReadingTypeCreationInterface
{
    public function handleSensorReadingTypeCreation(Sensor $sensor): array;
}
