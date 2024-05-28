<?php

namespace App\Sensors\Repository\OutOfBounds\ORM;

use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeHumid;
use App\Sensors\Repository\OutOfBounds\OutOfBoundsRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OutOfRangeHumid>
 *
 * @method OutOfRangeHumid|null find($id, $lockMode = null, $lockVersion = null)
 * @method OutOfRangeHumid|null findOneBy(array $criteria, array $orderBy = null)
 * @method OutOfRangeHumid[]    findAll()
 * @method OutOfRangeHumid[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OutOfBoundsHumidityRepository extends ServiceEntityRepository implements OutOfBoundsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OutOfRangeHumid::class);
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
