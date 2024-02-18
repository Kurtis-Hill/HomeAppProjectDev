<?php

namespace App\Sensors\Repository\Sensors;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @method SensorType|null find($id, $lockMode = null, $lockVersion = null)
 * @method SensorType|null findOneBy(array $criteria, array $orderBy = null)
 * @method SensorType[]    findAll()
 * @method SensorType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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


    #[ArrayShape([SensorType::class])]
    public function findAllSensorTypes(): array;

    /**
     * @throws ORMException
     */
    #[ArrayShape(['Bmp', 'Dallas', 'Dht', 'Soil'])]
    public function findAllSensorTypeNames(): array;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function flush(): void;

    /**
     * @throws  ORMException
     */
    public function remove(SensorType $sensorType): void;
}
