<?php

namespace App\Repository\Sensor\Sensors;

use App\Entity\Sensor\AbstractSensorType;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @method AbstractSensorType|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractSensorType|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractSensorType[]    findAll()
 * @method AbstractSensorType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface SensorTypeRepositoryInterface
{
    /**
     * @throws  ORMException | NonUniqueResultException
     */
    public function findOneById(int $id): ?AbstractSensorType;

    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(AbstractSensorType $sensorType): void;


    #[ArrayShape([AbstractSensorType::class])]
    public function findAllSensorTypes(): array;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function flush(): void;

    /**
     * @throws  ORMException
     */
    public function remove(AbstractSensorType $sensorType): void;
}
