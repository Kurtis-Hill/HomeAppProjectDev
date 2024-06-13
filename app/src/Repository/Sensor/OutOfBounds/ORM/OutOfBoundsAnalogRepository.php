<?php

namespace App\Repository\Sensor\OutOfBounds\ORM;

use App\Entity\Sensor\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Entity\Sensor\OutOfRangeRecordings\OutOfRangeAnalog;
use App\Repository\Sensor\OutOfBounds\OutOfBoundsRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<\App\Entity\Sensor\OutOfRangeRecordings\OutOfRangeAnalog>
 *
 * @method OutOfRangeAnalog|null find($id, $lockMode = null, $lockVersion = null)
 * @method OutOfRangeAnalog|null findOneBy(array $criteria, array $orderBy = null)
 * @method OutOfRangeAnalog[]    findAll()
 * @method OutOfRangeAnalog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutOfBoundsAnalogRepository extends ServiceEntityRepository implements OutOfBoundsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OutOfRangeAnalog::class);
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
