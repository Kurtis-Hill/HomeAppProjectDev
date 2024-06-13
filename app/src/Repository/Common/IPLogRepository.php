<?php

namespace App\Repository\Common;

use App\Entity\Common\IPLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<\App\Entity\Common\IPLog>
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

    public function persist(IPLog $ipLog): void
    {
        $this->_em->persist($ipLog);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function removeIPLogByIPAddress(string $ipAddress): void
    {
        $ipLog = $this->findOneBy(['ipAddress' => $ipAddress]);
        if ($ipLog !== null) {
            $this->_em->remove($ipLog);
            $this->_em->flush();
        }
    }
}
