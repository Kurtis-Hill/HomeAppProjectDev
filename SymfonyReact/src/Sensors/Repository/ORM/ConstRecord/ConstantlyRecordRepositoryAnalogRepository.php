<?php

namespace App\Sensors\Repository\ORM\ConstRecord;

use App\Sensors\Entity\ConstantRecording\ConstAnalog;
use App\Sensors\Entity\ConstantRecording\ConstantlyRecordInterface;
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
class ConstantlyRecordRepositoryAnalogRepository extends ServiceEntityRepository implements ConstantlyRecordRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstAnalog::class);
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
