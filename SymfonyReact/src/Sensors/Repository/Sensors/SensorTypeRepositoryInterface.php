<?php

namespace App\Sensors\Repository\Sensors;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\AbstractSensorType;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Soil;
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
