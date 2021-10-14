<?php

namespace App\ESPDeviceSensor\Repository\ORM\ReadingType;

use App\ESPDeviceSensor\Entity\ReadingTypes\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class HumidityRepository extends ServiceEntityRepository implements ReadingTypeRepositoryInterface
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;

        parent::__construct($registry, Humidity::class);
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
