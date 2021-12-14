<?php

namespace App\ESPDeviceSensor\Repository\ORM\Sensors;

use App\ESPDeviceSensor\Entity\SensorType;

interface SensorTypeRepositoryInterface
{
    public function findOneById(int $id): ?SensorType;
}
