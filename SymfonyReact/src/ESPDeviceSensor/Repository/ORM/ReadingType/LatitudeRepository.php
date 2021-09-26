<?php

namespace App\ESPDeviceSensor\Repository\ORM\ReadingType;

use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
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

    public function persist(AllSensorReadingTypeInterface $sensorReadingType)
    {
        $this->registry->getManager()->persist($sensorReadingType);
    }

    public function flush()
    {
        $this->registry->getManager()->flush();
    }
}
