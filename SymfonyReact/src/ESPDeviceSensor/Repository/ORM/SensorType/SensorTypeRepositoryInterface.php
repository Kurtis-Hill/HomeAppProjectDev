<?php

namespace App\ESPDeviceSensor\Repository\ORM\SensorType;

use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorInterface;

interface SensorTypeRepositoryInterface
{
    public function persist(SensorInterface $sensor): void;

    public function flush(): void;
}
