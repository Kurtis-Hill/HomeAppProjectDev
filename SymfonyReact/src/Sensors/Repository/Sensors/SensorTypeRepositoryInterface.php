<?php

namespace App\Sensors\Repository\Sensors;

use App\Sensors\Entity\SensorType;
use App\Sensors\Repository\Sensors\ORM\SensorRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @method SensorRepository|null find($id, $lockMode = null, $lockVersion = null)
 * @method SensorRepository|null findOneBy(array $criteria, array $orderBy = null)
 * @method SensorRepository[]    findAll()
 * @method SensorRepository[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
