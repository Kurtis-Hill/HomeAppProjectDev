<?php

namespace App\Repository\Core;

use App\Entity\Devices\Devices;
use App\Entity\Sensors\Sensors;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Sensors|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sensors|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sensors[]    findAll()
 * @method Sensors[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SensorsRepository extends EntityRepository
{
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
//        return $qb->getQuery()->getOneOrNullResult();
    }


    /**
     * @param Sensors $sensors
     * @param $sensorData
     * @return array
     */
    public function getSensorCardFormDataBySensor(Sensors $sensors, $sensorData): array
    {
        $qb = $this->createQueryBuilder('sensors');

        $sensorAlias = $this->prepareSensorTypeDataObjectsForQuery($sensorData, $qb, ['sensors', 'sensorNameID']);

        $qb->select($sensorAlias)
            ->where(
                $qb->expr()->eq('sensors.sensorNameID', ':id')
            )
            ->setParameters(['id' => $sensors]);

        $result = array_filter($qb->getQuery()->getResult());
        $result = array_values($result);

        return $result;
    }

    /**
     * @param Sensors $sensors
     * @param $sensorData
     * @return array
     */
    public function getSensorReadingTypeCardFormDataBySensor(Sensors $sensors, $sensorData): StandardSensorTypeInterface
    {
        $qb = $this->createQueryBuilder('sensors');

        $sensorAlias = $this->prepareSensorTypeDataObjectsForQuery($sensorData, $qb, ['sensors', 'sensorNameID']);

        $qb->select($sensorAlias)
            ->where(
                $qb->expr()->eq('sensors.sensorNameID', ':id')
            )
            ->setParameters(['id' => $sensors]);

        $result = array_filter($qb->getQuery()->getResult());
        $result = array_values($result);

        return $result[0];
    }

}
