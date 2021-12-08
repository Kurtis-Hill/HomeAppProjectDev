<?php

namespace App\ESPDeviceSensor\Repository\ORM\OutOfBounds;

use App\ESPDeviceSensor\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\ESPDeviceSensor\Entity\OutOfRangeRecordings\OutOfRangeTemp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OutOfBoundsTempORMRepository extends ServiceEntityRepository implements OutOfBoundsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OutOfRangeTemp::class);
    }

    public function persist(OutOfBoundsEntityInterface $outOfBoundsEntity): void
    {
        $this->getEntityManager()->persist($outOfBoundsEntity);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
