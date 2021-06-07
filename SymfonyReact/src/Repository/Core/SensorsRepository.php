<?php

namespace App\Repository\Core;

use App\Entity\Core\User;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\Sensors;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Sensors|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sensors|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sensors[]    findAll()
 * @method Sensors[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SensorsRepository extends EntityRepository
{
    private function prepareSensorTypeDataObjectsForQuery(array $sensors, $qb, array $joinCondition): string
    {
//        $qb->innerJoin(Sensors::class, 'sensors', Join::WITH, 'sensors.sensorNameID = cv.sensorNameID');

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
//        dd($sensorData);
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

    public function findAllSensorsByAssociatedGroups(User $user)
    {
        $qb = $this->createQueryBuilder('sensor');
        $expr = $qb->expr();

        $qb->select('sensor')
            ->innerJoin(Devices::class, 'device', Join::WITH, 'device.deviceNameID = sensor.deviceNameID')
            ->where(
                $expr->in('device.groupNameID', ':groupNameIds')
            )
            ->setParameters(['groupNameIds' => $user->getGroupNameIds()]);

//        dd($qb->getQuery()->getResult(), $user->getGroupNameIds());
    }



    /**
     * Add left join for additional sensors
     * needs refactor
     * @param Sensors $sensors
     * @param $sensorData
     * @return StandardSensorTypeInterface|null
     */
    public function getSensorCardFormDataBySensor(Sensors $sensors, $sensorData): ?StandardSensorTypeInterface
    {
        $qb = $this->createQueryBuilder('sensors');

        $sensorAlias = $this->prepareSensorTypeDataObjectsForQuery($sensorData, $qb, ['sensors', 'sensorNameID']);

        $qb->select($sensorAlias)
//            ->innerJoin(Icons::class, 'i', Join::WITH,'i.iconID = cv.cardIconID')
//            ->innerJoin(CardColour::class, 'cc', Join::WITH,'cc.colourID = cv.cardColourID')
//            ->innerJoin(Sensors::class, 's', Join::WITH,'s.sensorNameID = cv.sensorNameID')
//            ->innerJoin(Cardstate::class, 'cs', Join::WITH,'cs.cardStateID = cv.cardStateID')
//            ->innerJoin(SensorType::class, 'st', Join::WITH,'s.sensorTypeID = st.sensorTypeID')
            ->where(
                $qb->expr()->eq('sensors.sensorNameID', ':id')
            )
            ->setParameters(['id' => $sensors]);
        $result = array_filter($qb->getQuery()->getResult());
//dd($result, 'result');
//
        $result = array_values($result);
//dd($result, 'result');
//        dd($qb->getQuery()->getOneOrNullResult());
//        return $qb->getQuery()->getOneOrNullResult();
        return $result[0];
    }

}
