<?php

namespace App\Repository\Sensor\ConstRecord\ORM;

use App\Entity\Sensor\ConstantRecording\ConstAnalog;
use App\Entity\Sensor\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Repository\Sensor\ConstRecord\ConstantlyRecordRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConstAnalog>
 *
 * @method ConstAnalog|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConstAnalog|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConstAnalog[]    findAll()
 * @method ConstAnalog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstantlyRecordAnalogRepository extends ServiceEntityRepository implements ConstantlyRecordRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstAnalog::class);
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
