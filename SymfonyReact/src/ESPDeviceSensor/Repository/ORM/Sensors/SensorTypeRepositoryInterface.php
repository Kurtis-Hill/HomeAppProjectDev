<?php

namespace App\ESPDeviceSensor\Repository\ORM\Sensors;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;

interface SensorTypeRepositoryInterface
{
    /**
     * @throws  ORMException | NonUniqueResultException
     */
    public function findOneById(int $id): ?SensorType;

    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(SensorType $sensorType): void;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function flush(): void;

    /**
     * @throws  ORMException
     */
    public function remove(SensorType $sensorType): void;

    /**
     * @throws  ORMException
     */
    public function findAll();
}
