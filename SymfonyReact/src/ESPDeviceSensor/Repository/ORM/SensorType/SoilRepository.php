<?php

namespace App\ESPDeviceSensor\Repository\ORM\SensorType;

use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SoilRepository extends ServiceEntityRepository implements SensorTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Soil::class);
    }

    public function persist(SensorInterface $sensor): void
    {
        $this->getEntityManager()->persist($sensor);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
