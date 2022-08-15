<?php

namespace App\Sensors\Repository\ORM\ReadingType;

use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use App\Sensors\Entity\ReadingTypes\Temperature;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

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
    public function getOneBySensorNameID(int $sensorNameID);

//    public function findOneByNamr

//    public function findAllBySensorName(string $name);
}
