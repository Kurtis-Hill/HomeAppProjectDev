<?php

namespace App\ESPDeviceSensor\Repository\ORM\ConstRecord;

use App\ESPDeviceSensor\Entity\ConstantRecording\ConstantlyRecordInterface;
use App\ESPDeviceSensor\Entity\ConstantRecording\ConstTemp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ConstantlyRecordRepositoryTempRepository extends ServiceEntityRepository implements ConstantlyRecordRepositoryInterface
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;

        parent::__construct($registry, ConstTemp::class);
    }

    public function persist(ConstantlyRecordInterface $sensorReadingData): void
    {
        $this->registry->getManager()->persist($sensorReadingData);
    }

    public function flush(): void
    {
        $this->registry->getManager()->flush();
    }
}
