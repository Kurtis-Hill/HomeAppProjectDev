<?php

namespace App\ESPDeviceSensor\Repository\ORM\Sensors;

use App\Common\Traits\QueryJoinBuilderTrait;
use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\UserInterface\DTO\CardDataQueryDTO\JoinQueryDTO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;
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

    public function findOneById(int $id): ?Sensor
    {
        return $this->findOneBy(['sensorNameID' => $id]);
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

    public function getSensorReadingTypeDataBySensor(
        Sensor $sensors,
        array $sensorTypeJoinDTOs
    ): SensorTypeInterface
    {
        $qb = $this->createQueryBuilder(Sensor::ALIAS);

        $sensorAlias = $this->prepareSensorJoinsForQuery($sensorTypeJoinDTOs, $qb);

        $qb->select($sensorAlias)
            ->where(
                $qb->expr()->eq(Sensor::ALIAS. '.sensorNameID', ':id')
            )
            ->setParameters(['id' => $sensors]);

        $result = array_filter($qb->getQuery()->getResult());
        $result = array_values($result);

        return $result[0];
    }

    #[ArrayShape([
        Sensor::class | Dht::class | Bmp::class | Dallas::class | Soil::class,
        Temperature::class, Analog::class, Humidity::class, Latitude::class,
    ])]
    public function getSensorTypeAndReadingTypeObjectsForSensor(
        int $device,
        string $sensorsName,
        JoinQueryDTO $joinQueryDTO = null,
        array $readingTypeJoinQueryDTOs = [],
    ): array
    {
        $qb = $this->createQueryBuilder('sensors');

        if (!empty($readingTypeJoinQueryDTOs)) {
            $readingTypes = $this->prepareSensorJoinsForQuery($readingTypeJoinQueryDTOs, $qb);
            $selects[] = $readingTypes;
        }
        if ($joinQueryDTO !== null) {
            $sensorTypes = $this->prepareSensorJoinsForQuery([$joinQueryDTO], $qb);
            $selects[] = $sensorTypes;
        }

        $qb->select($selects ?? ['']);
            $qb->innerJoin(
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

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findSensorObjectByDeviceIdAndSensorName(int $deviceId, string $sensorName): ?Sensor
    {
        $qb = $this->createQueryBuilder('sensor');

        $qb->select('sensor')
            ->innerJoin(Devices::class, 'device', Join::WITH, 'device.deviceNameID = sensor.deviceNameID')
            ->where(
                $qb->expr()->eq('sensor.sensorName', ':sensorName'),
                $qb->expr()->eq('device.deviceNameID', ':deviceID')
            )
            ->setParameters(
                [
                    'sensorName' => $sensorName,
                    'deviceID' => $deviceId,
                ]
            );

        return $qb->getQuery()->getOneOrNullResult();
    }

    #[Deprecated]
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
