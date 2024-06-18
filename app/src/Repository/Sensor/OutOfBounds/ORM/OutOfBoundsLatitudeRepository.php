<?php

namespace App\Repository\Sensor\OutOfBounds\ORM;

use App\Entity\Sensor\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Entity\Sensor\OutOfRangeRecordings\OutOfRangeLatitude;
use App\Repository\Sensor\OutOfBounds\OutOfBoundsRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OutOfRangeLatitude>
 *
 * @method OutOfRangeLatitude|null find($id, $lockMode = null, $lockVersion = null)
 * @method OutOfRangeLatitude|null findOneBy(array $criteria, array $orderBy = null)
 * @method OutOfRangeLatitude[]    findAll()
 * @method OutOfRangeLatitude[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutOfBoundsLatitudeRepository extends ServiceEntityRepository implements OutOfBoundsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OutOfRangeLatitude::class);
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
