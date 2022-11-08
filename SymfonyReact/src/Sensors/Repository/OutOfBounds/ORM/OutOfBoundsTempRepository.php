<?php

namespace App\Sensors\Repository\OutOfBounds\ORM;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeTemp;
use App\Sensors\Repository\OutOfBounds\OutOfBoundsRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OutOfRangeTemp>
 *
 * @method OutOfRangeTemp|null find($id, $lockMode = null, $lockVersion = null)
 * @method OutOfRangeTemp|null findOneBy(array $criteria, array $orderBy = null)
 * @method OutOfRangeTemp[]    findAll()
 * @method OutOfRangeTemp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutOfBoundsTempRepository extends ServiceEntityRepository implements OutOfBoundsRepositoryInterface
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
