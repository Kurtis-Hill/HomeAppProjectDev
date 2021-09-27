<?php

namespace App\ESPDeviceSensor\Repository\ORM\SensorType;

use App\Entity\Sensors\SensorTypes\Soil;
use App\ESPDeviceSensor\Repository\ORM\ReadingType\ReadingTypeRepositoryInterface;
use App\HomeAppSensorCore\Interfaces\SensorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SoilRepository extends ServiceEntityRepository implements SensorTypeRepositoryInterface
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
        parent::__construct($registry, Soil::class);
    }

    public function persist(SensorInterface $sensor): void
    {
        $this->registry->getManager()->persist($sensor);
    }

    public function flush(): void
    {
        $this->registry->getManager()->flush();
    }
}
