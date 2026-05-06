<?php

namespace App\Repository\Sensor\ConstRecord\ORM;

use App\Entity\Sensor\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Entity\Sensor\ConstantRecording\ConstTemp;
use App\Repository\Sensor\ConstRecord\ConstantlyRecordRepositoryInterface;
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
class ConstantlyRecordTempRepository extends ServiceEntityRepository implements ConstantlyRecordRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstTemp::class);
    }

    public function persist(ConstantlyRecordEntityInterface $sensorReadingData): void
    {
        $this->getEntityManager()->persist($sensorReadingData);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
