<?php

namespace App\Services\ESPDeviceSensor\SensorData\Repository\ORM;

use App\Entity\Sensors\ConstantRecording\ConstTemp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ConstTempORMRepository extends ServiceEntityRepository
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;

        parent::__construct($registry, ConstTemp::class);
    }

    public function saveCurrentRecordUpdateReading(mixed $sensorReadingData): void
    {
        $this->registry->getManager()->persist($sensorReadingData);
    }

    public function persistUpdateReadingData(): void
    {
        $this->registry->getManager()->flush();
    }
}
