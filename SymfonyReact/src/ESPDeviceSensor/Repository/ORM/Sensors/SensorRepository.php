<?php

namespace App\ESPDeviceSensor\Repository\ORM\Sensors;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\Entity\Sensors;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class SensorRepository extends ServiceEntityRepository implements SensorRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sensors::class);
    }

    public function persist(Sensors $sensorReadingData): void
    {
        $this->getEntityManager()->persist($sensorReadingData);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Sensors $sensors): void
    {
        $this->getEntityManager()->remove($sensors);
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

    public function getSelectedSensorReadingTypeObjectsBySensorNameAndDevice(Devices $device, string $sensors, array $sensorData): array
    {
        $qb = $this->createQueryBuilder('sensors');
        $sensorAlias = $this->prepareSensorTypeDataObjectsForQuery($sensorData, $qb, ['sensors', 'sensorNameID']);

        $qb->select($sensorAlias)
            ->innerJoin(
                Devices::class,
                'device',
                Join::WITH,
                'sensors.deviceNameID = device.deviceNameID'
            )
            ->where(
                $qb->expr()->eq('sensors.sensorName', ':sensorName'),
                $qb->expr()->eq('sensors.deviceNameID', ':deviceID')
            )
            ->setParameters(['sensorName' => $sensors, 'deviceID' => $device]);

        $result = array_filter($qb->getQuery()->getResult());
        $result = array_values($result);

        return $result;
    }

    private function prepareSensorTypeDataObjectsForQuery(array $sensors, QueryBuilder $qb, array $joinCondition): string
    {
        $joinConditionString = '.' .$joinCondition[1]. ' = ' .$joinCondition[0]. '.' .$joinCondition[1];

        $sensorAlias = [];
        foreach ($sensors as $sensorNames => $sensorData) {
            $sensorAlias[] = $sensorData['alias'];
            $qb->leftJoin($sensorData['object'], $sensorData['alias'], Join::WITH, $sensorData['alias'].$joinConditionString);
        }

        return implode(', ', $sensorAlias);
    }
}
