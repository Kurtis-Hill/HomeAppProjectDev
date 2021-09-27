<?php

namespace App\ESPDeviceSensor\Repository\ORM\ConstRecord;

use App\Entity\Sensors\ConstantRecording\ConstAnalog;
use App\Entity\Sensors\ConstantRecording\ConstantlyRecordInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ConstantlyRecordRepositoryAnalogRepositroy extends ServiceEntityRepository implements ConstantlyRecordRepositoryInterface
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
