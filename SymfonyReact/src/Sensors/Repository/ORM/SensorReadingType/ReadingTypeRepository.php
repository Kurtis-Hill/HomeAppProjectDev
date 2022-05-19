<?php

namespace App\Sensors\Repository\ORM\SensorReadingType;

use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReadingTypeRepository extends ServiceEntityRepository implements ReadingTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReadingTypes::class);
    }
}
