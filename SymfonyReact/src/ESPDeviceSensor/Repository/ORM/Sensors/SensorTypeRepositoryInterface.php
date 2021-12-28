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

    public function persist(SensorType $sensorType): void;

    /**
     * @throws  ORMException
     */
    public function flush(): void;

    /**
     * @throws  ORMException
     */
    public function remove(SensorType $sensorType): void;
}
