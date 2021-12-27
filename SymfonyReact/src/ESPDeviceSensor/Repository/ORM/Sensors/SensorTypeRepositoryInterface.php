<?php

namespace App\ESPDeviceSensor\Repository\ORM\Sensors;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use Doctrine\ORM\ORMException;

interface SensorTypeRepositoryInterface
{
    /**
     * @throws  ORMException
     */
    public function findOneById(int $id): ?SensorType;
}
