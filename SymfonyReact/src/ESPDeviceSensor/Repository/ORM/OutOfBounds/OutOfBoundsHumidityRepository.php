<?php

namespace App\ESPDeviceSensor\Repository\ORM\OutOfBounds;

use App\ESPDeviceSensor\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\ESPDeviceSensor\Entity\OutOfRangeRecordings\OutOfRangeHumid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OutOfBoundsHumidityRepository extends ServiceEntityRepository implements OutOfBoundsRepositoryInterface
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
        parent::__construct($registry, OutOfRangeHumid::class);
    }

    public function persist(OutOfBoundsEntityInterface $outOfBoundsEntity): void
    {
        $this->registry->getManager()->persist($outOfBoundsEntity);
    }

    public function flush(): void
    {
        $this->registry->getManager()->flush();
    }
}
