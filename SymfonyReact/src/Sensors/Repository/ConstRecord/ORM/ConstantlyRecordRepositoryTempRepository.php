<?php

namespace App\Sensors\Repository\ConstRecord\ORM;

use App\Sensors\Entity\ConstantRecording\ConstantlyRecordInterface;
use App\Sensors\Entity\ConstantRecording\ConstTemp;
use App\Sensors\Repository\ConstRecord\ConstantlyRecordRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConstTemp>
 *
 * @method ConstTemp|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConstTemp|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConstTemp[]    findAll()
 * @method ConstTemp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstantlyRecordRepositoryTempRepository extends ServiceEntityRepository implements ConstantlyRecordRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstTemp::class);
    }

    public function persist(ConstantlyRecordInterface $sensorReadingData): void
    {
        $this->getEntityManager()->persist($sensorReadingData);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
