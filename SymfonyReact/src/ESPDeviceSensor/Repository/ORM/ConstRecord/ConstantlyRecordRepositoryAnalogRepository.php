<?php

namespace App\ESPDeviceSensor\Repository\ORM\ConstRecord;

use App\ESPDeviceSensor\Entity\ConstantRecording\ConstAnalog;
use App\ESPDeviceSensor\Entity\ConstantRecording\ConstantlyRecordInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ConstantlyRecordRepositoryAnalogRepository extends ServiceEntityRepository implements ConstantlyRecordRepositoryInterface
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;

        parent::__construct($registry, ConstAnalog::class);
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
