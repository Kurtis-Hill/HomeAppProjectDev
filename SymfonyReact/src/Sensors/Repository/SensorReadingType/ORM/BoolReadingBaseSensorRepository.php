<?php

namespace App\Sensors\Repository\SensorReadingType\ORM;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\AbstractBoolReadingBaseSensor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReadingTypeRepository>
 *
 * @method AbstractBoolReadingBaseSensor|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractBoolReadingBaseSensor|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractBoolReadingBaseSensor[]    findAll()
 * @method AbstractBoolReadingBaseSensor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoolReadingBaseSensorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractBoolReadingBaseSensor::class);
    }
}
