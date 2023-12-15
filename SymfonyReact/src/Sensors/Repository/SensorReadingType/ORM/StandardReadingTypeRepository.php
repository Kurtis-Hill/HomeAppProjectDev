<?php

namespace App\Sensors\Repository\SensorReadingType\ORM;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\AbstractStandardReadingType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReadingTypeRepository>
 *
 * @method AbstractStandardReadingType|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractStandardReadingType|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractStandardReadingType[]    findAll()
 * @method AbstractStandardReadingType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StandardReadingTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractStandardReadingType::class);
    }
}
