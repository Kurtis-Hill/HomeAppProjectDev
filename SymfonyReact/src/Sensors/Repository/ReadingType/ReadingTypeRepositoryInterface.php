<?php

namespace App\Sensors\Repository\ReadingType;

use App\Sensors\Entity\ReadingTypes\BaseReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;

/**
 * @method Analog|Humidity|Latitude|Temperature|null find($id, $lockMode = null, $lockVersion = null)
 * @method Analog|Humidity|Latitude|Temperature|null findOneBy(array $criteria, array $orderBy = null)
 * @method Analog[]|Humidity[]|Latitude[]|Temperature[]    findAll()
 * @method Analog[]|Humidity[]|Latitude[]|Temperature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface ReadingTypeRepositoryInterface
{
    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(AllSensorReadingTypeInterface $readingTypeObject): void;

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function flush(): void;

    public function findOneById(int $id);

    /**
     * @throws NonUniqueResultException
     */
    public function findOneBySensorNameID(int $sensorNameID): ?AllSensorReadingTypeInterface;

    public function findOneBySensorName(string $sensorName): ?AllSensorReadingTypeInterface;

    public function refresh(AllSensorReadingTypeInterface $readingTypeObject): void;

    /**
     * @param int $sensorID
     *
     * @return BaseReadingTypeInterface[]
     */
    public function findBySensorID(int $sensorID): array;
}
