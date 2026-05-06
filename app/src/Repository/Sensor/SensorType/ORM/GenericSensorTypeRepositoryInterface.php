<?php

namespace App\Repository\Sensor\SensorType\ORM;

use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use App\Entity\Sensor\SensorTypes\Soil;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;

/**
 * @method Bmp|Dallas|Dht|Soil|GenericRelay|GenericMotion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bmp|Dallas|Dht|Soil|GenericRelay|GenericMotion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bmp[]|Dallas[]|Dht[]|Soil[]|GenericRelay[]|GenericMotion[]    findAll()
 * @method Bmp[]|Dallas[]|Dht[]|Soil[]|GenericRelay[]|GenericMotion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
