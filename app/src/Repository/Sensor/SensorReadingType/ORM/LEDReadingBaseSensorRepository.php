<?php

namespace App\Repository\Sensor\SensorReadingType\ORM;

use App\Entity\Sensor\ReadingTypes\LEDReadingTypes\AbstractLEDSensorType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReadingTypeRepository>
 *
 * @method AbstractLEDSensorType|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractLEDSensorType|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractLEDSensorType[]    findAll()
 * @method AbstractLEDSensorType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LEDReadingBaseSensorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractLEDSensorType::class);
    }
}
