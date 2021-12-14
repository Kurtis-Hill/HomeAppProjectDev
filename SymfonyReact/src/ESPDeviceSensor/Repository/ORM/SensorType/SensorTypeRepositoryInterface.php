<?php

namespace App\ESPDeviceSensor\Repository\ORM\SensorType;

use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use Doctrine\ORM\ORMException;

interface SensorTypeRepositoryInterface
{
    public function persist(SensorTypeInterface $sensor): void;

    /**
     * @throws ORMException
     */
    public function flush(): void;
}
