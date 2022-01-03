<?php

namespace App\ESPDeviceSensor\Repository\ORM\Sensors;

use App\Common\Traits\QueryJoinBuilderTrait;
use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\Deprecated;

class SensorRepository extends ServiceEntityRepository implements SensorRepositoryInterface
{
    use QueryJoinBuilderTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sensor::class);
    }

    public function persist(Sensor $sensorReadingData): void
    {
        $this->getEntityManager()->persist($sensorReadingData);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Sensor $sensors): void
    {
        $this->getEntityManager()->remove($sensors);
    }

    public function checkForDuplicateSensorOnDevice(Sensor $sensorData): ?Sensor
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
                    'groupName' => $sensorData->getDeviceObject()->getGroupNameObject(),
                ]
            );

        return $qb->getQuery()->getResult()[0] ?? null;
    }

    public function getSensorReadingTypeDataBySensor(Sensor $sensors, array $sensorTypeJoinDTOs): SensorTypeInterface
    {
        $qb = $this->createQueryBuilder(Sensor::ALIAS);

        $sensorAlias = $this->prepareSensorJoinsForQuery($sensorTypeJoinDTOs, $qb);
//dd($sensorAlias);
//        if (!empty($sensorTypeJoinDTOs)) {
//            $readingAlias = $this->prepareSensorJoinsForQuery($sensorTypeJoinDTOs, $qb);
//            $qb->addSelect($readingAlias);
//        }

        $qb->select($sensorAlias)
            ->where(
                $qb->expr()->eq(Sensor::ALIAS. '.sensorNameID', ':id')
            )
            ->setParameters(['id' => $sensors]);

        $result = array_filter($qb->getQuery()->getResult());
        $result = array_values($result);
        return $result[0];
    }

    public function getSensorTypeAndReadingTypeObjectsForSensor(JoinQueryDTO $joinQueryDTO, int $device, array $readingTypeJoinQueryDTOs, string $sensorsName): array
    {
        $qb = $this->createQueryBuilder('sensors');

        $readingTypes = $this->prepareSensorJoinsForQuery($readingTypeJoinQueryDTOs, $qb);
        $readingTypeAlias = $this->prepareSensorJoinsForQuery([$joinQueryDTO], $qb);

        $qb->select($readingTypeAlias, $readingTypes)
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
            ->setParameters(['sensorName' => $sensorsName, 'deviceID' => $device]);

        return $qb->getQuery()->getResult();
    }

    #[Deprecated]
    //can remove after sensor boundry work is done
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

    #[Deprecated]
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
