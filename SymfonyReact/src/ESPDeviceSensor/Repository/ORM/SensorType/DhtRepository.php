<?php

namespace App\ESPDeviceSensor\Repository\ORM\SensorType;

use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DhtRepository extends ServiceEntityRepository implements SensorTypeRepositoryInterface
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
        parent::__construct($registry, Dht::class);
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
