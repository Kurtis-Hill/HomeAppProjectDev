<?php

namespace App\ESPDeviceSensor\Repository\ORM\ConstRecord;

use App\ESPDeviceSensor\Entity\ConstantRecording\ConstantlyRecordInterface;
use App\ESPDeviceSensor\Entity\ConstantRecording\ConstHumid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ConstantlyRecordRepositoryHumidRepository extends ServiceEntityRepository implements ConstantlyRecordRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstHumid::class);
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
