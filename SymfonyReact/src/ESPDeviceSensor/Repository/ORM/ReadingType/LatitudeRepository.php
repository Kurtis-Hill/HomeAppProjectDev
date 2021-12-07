<?php

namespace App\ESPDeviceSensor\Repository\ORM\ReadingType;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LatitudeRepository extends ServiceEntityRepository implements ReadingTypeRepositoryInterface
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;

        parent::__construct($registry, Latitude::class);
    }

    public function persist(AllSensorReadingTypeInterface $sensorReadingType): void
    {
        $this->registry->getManager()->persist($sensorReadingType);
    }

    public function flush(): void
    {
        $this->registry->getManager()->flush();
    }
}
