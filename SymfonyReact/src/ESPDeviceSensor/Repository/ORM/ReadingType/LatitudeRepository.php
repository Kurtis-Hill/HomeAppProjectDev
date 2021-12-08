<?php

namespace App\ESPDeviceSensor\Repository\ORM\ReadingType;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LatitudeRepository extends ServiceEntityRepository implements ReadingTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Latitude::class);
    }

    public function persist(AllSensorReadingTypeInterface $sensorReadingType): void
    {
        $this->getEntityManager()->persist($sensorReadingType);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
