<?php

namespace App\Sensors\Repository\ORM\ConstRecord;

use App\Sensors\Entity\ConstantRecording\ConstantlyRecordInterface;
use App\Sensors\Entity\ConstantRecording\ConstTemp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
