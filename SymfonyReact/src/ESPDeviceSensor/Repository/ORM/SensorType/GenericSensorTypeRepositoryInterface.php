<?php

namespace App\ESPDeviceSensor\Repository\ORM\SensorType;

use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;

interface GenericSensorTypeRepositoryInterface
{
    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(SensorTypeInterface $sensor): void;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function flush(): void;
}
