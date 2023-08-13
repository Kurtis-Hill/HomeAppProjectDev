<?php

namespace App\Common\Repository;

use App\Common\Entity\IPLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IPLog>
 *
 * @method IPLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method IPLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method IPLog[]    findAll()
 * @method IPLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IPLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IPLog::class);
    }
}
