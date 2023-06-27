<?php

namespace App\Sensors\Repository\SensorType\ORM;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Soil;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;

/**
 * @method Bmp|Dallas|Dht|Soil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bmp|Dallas|Dht|Soil|Motion|Relay|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bmp[]|Dallas[]|Dht[]|Soil[]    findAll()
 * @method Bmp[]|Dallas[]|Dht[]|Soil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
