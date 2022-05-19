<?php

namespace App\Sensors\Repository\ORM\Sensors;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
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
     * @throws ORMException
     */
    public function getAllSensorTypeNames(): array;

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
