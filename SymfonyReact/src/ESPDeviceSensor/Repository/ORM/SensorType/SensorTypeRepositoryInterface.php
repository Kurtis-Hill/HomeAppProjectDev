<?php

namespace App\ESPDeviceSensor\Repository\ORM\SensorType;

use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorInterface;

interface SensorTypeRepositoryInterface
{
    public function persist(SensorInterface $sensor): void;

    public function flush(): void;

//    public function contains(SensorInterface $sensor): bool;
//
//    public function remove(SensorInterface $sensorReadingType): bool;
}
