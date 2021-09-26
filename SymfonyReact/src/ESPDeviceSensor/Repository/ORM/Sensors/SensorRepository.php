<?php

namespace App\ESPDeviceSensor\Repository\ORM\Sensors;

use App\Entity\Devices\Devices;
use App\Entity\Sensors\ConstantRecording\ConstTemp;
use App\Entity\Sensors\Sensors;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

class SensorRepository extends ServiceEntityRepository
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;

        parent::__construct($registry, Sensors::class);
    }

    public function persist(Sensors $sensorReadingData): void
    {
        $this->registry->getManager()->persist($sensorReadingData);
    }

    public function flush(): void
    {
        $this->registry->getManager()->flush();
    }

    public function checkForDuplicateSensorOnDevice(Sensors $sensorData): ?Sensors
    {
        $qb = $this->createQueryBuilder('sensor');
        $expr = $qb->expr();

        $qb->select('sensor')
            ->innerJoin(Devices::class, 'device', Join::WITH, 'device.deviceNameID = sensor.deviceNameID')
            ->where(
                $expr->eq('sensor.sensorName', ':sensorName'),
                $expr->eq('device.groupNameID', ':groupName'),
            )
            ->setParameters(
                [
                    'sensorName' => $sensorData->getSensorName(),
                    'groupName' => $sensorData->getDeviceNameID()->getGroupNameObject(),
                ]
            );

        return $qb->getQuery()->getResult()[0] ?? null;
    }
}
