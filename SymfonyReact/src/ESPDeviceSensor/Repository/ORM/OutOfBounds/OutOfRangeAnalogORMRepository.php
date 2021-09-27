<?php

namespace App\ESPDeviceSensor\Repository\ORM\OutOfBounds;

use App\Entity\Sensors\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Entity\Sensors\OutOfRangeRecordings\OutOfRangeAnalog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OutOfRangeAnalogORMRepository extends ServiceEntityRepository implements OutOfBoundsRepositoryInterface
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
        parent::__construct($registry, OutOfRangeAnalog::class);
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
