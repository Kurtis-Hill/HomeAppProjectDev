<?php


namespace App\Repository\Card;


use App\Entity\Card\CardColour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\Icons;
use App\Entity\Sensors\SensorType;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use function Doctrine\ORM\QueryBuilder;
use App\Entity\Sensors\Sensors;
use App\Entity\Devices\Devices;



class CardViewRepository extends EntityRepository
{
    private function prepareSensorDataForQuery(array $sensors, $qb, array $joinCondition): string
    {
        $joinConditionString = '.' .$joinCondition[1]. ' = ' .$joinCondition[0]. '.' .$joinCondition[1];

        $sensorAlias = [];
        foreach ($sensors as $sensorNames => $sensorData) {
//            dd($sensorData['object'], $sensorData['alias']);
            $sensorAlias[] = $sensorData['alias'];
            $qb->leftJoin($sensorData['object'], $sensorData['alias'], Join::WITH,$sensorData['alias'].$joinConditionString);
        }

        return implode(', ', $sensorAlias);
    }


    public function getAllCardObjectsForUser(int $userID, array $groupNameIDs, array $sensors)
    {
        $cardViewOne = Cardstate::ON;
        $cardViewTwo = Cardstate::INDEX_ONLY;

        $qb = $this->createQueryBuilder('cv');
        $expr = $qb->expr();

        $sensorAlias = $this->prepareSensorDataForQuery($sensors, $qb, ['cv', 'cardViewID']);

        $qb->select($sensorAlias)
            ->innerJoin(Sensors::class, 'sensors', Join::WITH, 'sensors.sensorNameID = cv.sensorNameID')
            ->innerJoin(Devices::class, 'devices', Join::WITH, 'sensors.deviceNameID = devices.deviceNameID');

        $qb->where(
            $expr->orX(
                $expr->eq('cv.cardStateID', ':cardviewOne'),
                $expr->eq('cv.cardStateID', ':cardviewTwo')
            ),
            $expr->eq('cv.userID', ':userID'),
            $expr->in('devices.groupNameID', ':groupNameID')
        );

        $qb->setParameters(
            [
                'userID' => $userID,
                'groupNameID' => $groupNameIDs,
                'cardviewOne' => $cardViewOne,
                'cardviewTwo' => $cardViewTwo
            ]
        );

        return array_filter($qb->getQuery()->getResult());
    }


    /**
     * @param array $groupNameIDs
     * @param int $userID
     * @param integer $deviceDetails
     * @param array $sensors
     * @return array
     */
    public function getAllCardReadingsForDevice(int $userID, array $groupNameIDs, array $sensors, int $deviceDetails): array
    {
//        dd($deviceDetails, $groupNameIDs, $userID, $sensors);
        $cardViewOne = Cardstate::ON;
        $cardViewTwo = Cardstate::INDEX_ONLY;

        $qb = $this->createQueryBuilder('cv');
        $expr = $qb->expr();

        $sensorAlias = $this->prepareSensorDataForQuery($sensors, $qb, ['cv', 'cardViewID']);
//dd($sensorAlias);
        $qb->select($sensorAlias)
            ->innerJoin(Sensors::class, 'sensors', Join::WITH, 'sensors.sensorNameID = cv.sensorNameID')
            ->innerJoin(Devices::class, 'devices', Join::WITH, 'sensors.deviceNameID = devices.deviceNameID');

        $qb->where(
            $expr->orX(
                $expr->eq('cv.cardStateID', ':cardviewOne'),
                $expr->eq('cv.cardStateID', ':cardviewTwo')
            ),
            $expr->eq('cv.userID', ':userID'),
            $expr->in('devices.groupNameID', ':groupNameID'),
            $expr->eq('devices.deviceNameID', ':deviceNameID'),
        );
        // dd($sensorAlias, $groupNameIDs, $sensors, $qb);
        $qb->setParameters(
            [
                'userID' => $userID,
                'groupNameID' => $groupNameIDs,
                'deviceNameID' => $deviceDetails,
                'cardviewOne' => $cardViewOne,
                'cardviewTwo' => $cardViewTwo
            ]
        );

//        dd($qb->getQuery()->getSQL());
        return array_filter($qb->getQuery()->getResult());
        //dd($groupNameIDs, $userID, $deviceDetails, $sensors);
//        $qb = $this->createQueryBuilder('cv');
//        $expr = $qb->expr();
//
//        $this->prepareSensorDataForQuery($sensors, $qb, ['cv', 'cardViewID']);
//
//        $sensorAlias = $this->prepareSensorDataForQuery($sensors, $qb, ['cv', 'cardViewID']);
//
//        $qb->select($sensorAlias)
//        ->innerJoin(Sensors::class, 'sensors', Join::WITH, 'sensors.sensorNameID = cv.sensorNameID')
//        ->innerJoin(Devices::class, 'devices', Join::WITH, 'sensors.deviceNameID = devices.deviceNameID')
//        ->where(
//            $qb->expr()->in('devices.groupNameID', ':groupNameID'),
//            $expr->eq('cv.userID', ':userID'),
//            $expr->eq('devices.deviceNameID', ':deviceNameID'),
//        );
//
//        $qb->setParameters([
//            'deviceNameID' => $deviceDetails,
//            'userID' => $userID,
//            'groupNameID' => $groupNameIDs,
//        ]);
//
        return array_filter($qb->getQuery()->getResult());
    }


    /**
     * Add left join for additional sensors
     * needs refactor
     * @param array $criteria
     * @param $sensorData
     * @return mixed
     */
    public function getSensorCardFormData(array $criteria, $sensorData): StandardSensorTypeInterface
    {
        $qb = $this->createQueryBuilder('cv');

        $sensorAlias = $this->prepareSensorDataForQuery($sensorData, $qb, ['cv', 'cardViewID']);

        $qb->select($sensorAlias)
            ->innerJoin(Icons::class, 'i', Join::WITH,'i.iconID = cv.cardIconID')
            ->innerJoin(CardColour::class, 'cc', Join::WITH,'cc.colourID = cv.cardColourID')
            ->innerJoin(Sensors::class, 's', Join::WITH,'s.sensorNameID = cv.sensorNameID')
            ->innerJoin(Cardstate::class, 'cs', Join::WITH,'cs.cardStateID = cv.cardStateID')
            ->innerJoin(SensorType::class, 'st', Join::WITH,'s.sensorTypeID = st.sensorTypeID')
            ->where(
                $qb->expr()->eq('cv.cardViewID', ':id')
            )
            ->setParameters(['id' => $criteria['id']]);

        $result = array_filter($qb->getQuery()->getResult());

        $result = array_values($result);

        return $result[0];
    }


    /**
     * @param array $criteria
     * @param array $sensorData
     * @return mixed
     */
    public function getUsersCurrentlySelectedSensorsCardData(array $criteria, array $sensorData): array
    {
        $qb = $this->createQueryBuilder('cv');

        $sensorAlias = $this->prepareSensorDataForQuery($sensorData, $qb, ['cv', 'sensorNameID']);

        $qb->select('cv, ' .$sensorAlias)
            ->where(
                $qb->expr()->eq('cv.cardViewID', ':id'),
                $qb->expr()->eq('cv.userID', ':userID')
            )
            ->setParameters(['id' => $criteria['id'], 'userID' => $criteria['userID']]);

        $result = array_filter($qb->getQuery()->getResult());

        $result = array_values($result);

        return $result;
    }

//    public function getRandomCardViewDefaultData()
//    {
//        $qb = $this->createQueryBuilder('cv');
//        $expr = $qb->expr();
//
//        $qb->select('i', 'cc')
//            ->innerJoin(Icons::class, 'i', Join::WITH,'i.iconID = cv.cardIconID')
//            ->innerJoin(CardColour::class, 'cc', Join::WITH,'cc.colourID = cv.cardColourID')
//            ->where(
//                $expr->eq(
//                    'i.iconID', ':iconID',
//                ),
//                $expr->eq(
//                    'cc.colourID', ':colourID'
//                )
//            )
//            ->setParameters(
//                [
//                    'iconID' => random_int(1, 28),
//                    'colourID' => random_int(1, 4)
//                ]
//            );
//
//        dd($qb->getQuery()->getResult());
//        return $qb->getQuery()->getResult();
//    }
//    /**
//     * @param $groupNameIDs
//     * @param $userID
//     * @param null $type
//     * @return array|mixed
//     */
//    public function getAllCardReadingsIndex($groupNameIDs, $userID, $type = null): array
//    {
//        $cardViewOne = Cardstate::ON;
//        $cardViewTwo = Cardstate::INDEX_ONLY;
//
//        $qb = $this->createQueryBuilder('cv');
//        $expr = $qb->expr();
//
//        $qb->select(
//         't.tempid',
//                't.tempReading',
//                't.highTempReading',
//                't.lowTempReading',
//                't.tempTime',
//                'h.humidid',
//                'h.humidReading',
//                'h.highHumidReading',
//                'h.lowHumidReading',
//                'h.humidTime',
//                'a.analogid',
//                'a.analogReading',
//                'a.highAnalogReading',
//                'a.lowAnalogReading',
//                'a.analogTime',
//                'r.room',
//                'i.iconname',
////                's.sensorname',
//                'st.sensortype',
//                'cc.colour',
//                'cv.cardviewid'
//            )
//            ->innerJoin('App\Entity\Core\Room', 'r', Join::WITH,'r.roomid = cv.roomid')
//            ->innerJoin('App\Entity\Core\Icons', 'i', Join::WITH,'i.iconid = cv.cardiconid')
//            ->innerJoin('App\Entity\Card\CardColour', 'cc', Join::WITH,'cc.colourid = cv.cardcolourid')
////            ->innerJoin('App\Entity\Core\Sensors', 's', Join::WITH,'s.sensornameid = cv.sensornameid')
//            ->innerJoin('App\Entity\Core\SensorType', 'st', Join::WITH,'st.sensortypeid = s.sensortypeid')
//            ->leftJoin('App\Entity\Sensors\Temp', 't', Join::WITH,'t.sensornameid = cv.sensornameid')
//            ->leftJoin('App\Entity\Sensors\Humid', 'h', Join::WITH,'h.sensornameid = cv.sensornameid')
//            ->leftJoin('App\Entity\Sensors\Analog', 'a', Join::WITH,'a.sensornameid = cv.sensornameid')
//            ->where(
//                 $expr->orX(
//                     $expr->eq('cv.cardstateid', ':cardviewOne'),
//                     $expr->eq('cv.cardstateid', ':cardviewTwo')
//                 ),
//                 $expr->eq('cv.userid', ':userid'),
//                 $expr->in('s.groupnameid', ':groupNameID')
//            );
//
//            $qb->setParameters(
//                [
//                    'userid' => $userID,
//                    'groupNameID' => $groupNameIDs,
//                    'cardviewOne' => $cardViewOne,
//                    'cardviewTwo' => $cardViewTwo
//                ]
//            );
//
//        return $qb->getQuery()->getResult();
//    }
//
//
//    /**
//     * @param array $groupNameIDs
//     * @param int $userID
//     * @param $deviceDetails
//     * @param null $type
//     * @return array
//     */
//    public function getAllCardReadingsForRoom(array $groupNameIDs, int $userID, $deviceDetails, $type = null): array
//    {
//        $cardViewOne = Cardstate::ON;
//        $cardViewTwo = Cardstate::ROOM_ONLY;
//
//        $qb = $this->createQueryBuilder('cv');
//        $qb->select('t', 'h', 'a', 'r.room', 'i.iconname', 's.sensorname', 'cc.colour', 'cv.cardviewid')
//            ->leftJoin('App\Entity\Sensors\Temp', 't', Join::WITH,'t.sensornameid = cv.sensornameid')
//            ->leftJoin('App\Entity\Sensors\Humid', 'h', Join::WITH,'h.sensornameid = cv.sensornameid')
//            ->leftJoin('App\Entity\Sensors\Analog', 'a', Join::WITH,'a.sensornameid = cv.sensornameid')
//            ->innerJoin('App\Entity\Core\Room', 'r', Join::WITH,'r.roomid = cv.roomid')
//            ->innerJoin('App\Entity\Core\Icons', 'i', Join::WITH,'i.iconid = cv.cardiconid')
//            ->innerJoin('App\Entity\Card\CardColour', 'cc', Join::WITH,'cc.colourid = cv.cardcolourid')
//            ->innerJoin('App\Entity\Core\Sensors', 's', Join::WITH,'s.sensornameid = cv.sensornameid')
//            ->innerJoin('App\Entity\Core\Devices', 'dv', Join::WITH,'s.sensornameid = dv.devicenameid')
//        ;
//        $qb->where(
//            $qb->expr()->orX(
//                $qb->expr()->eq('cv.cardstateid', ':cardviewOne'),
//                $qb->expr()->eq('cv.cardstateid', ':cardviewTwo')
//            ),
//            $qb->expr()->in('s.groupnameid', ':groupNameID'),
//            $qb->expr()->eq('s.devicenameid', ':deviceNameID'),
//            $qb->expr()->eq('cv.userid', ':userid'),
//            $qb->expr()->eq('s.groupnameid', ':deviceGroup'),
//            $qb->expr()->eq('cv.roomid', ':deviceRoom')
//        );
//        $qb->setParameters([
//            'deviceNameID' => $deviceDetails['deviceName'],
//            'deviceGroup' => $deviceDetails['deviceGroup'],
//            'deviceRoom' => $deviceDetails['deviceRoom'],
//            'userid' => $userID,
//            'groupNameID' => $groupNameIDs,
//            'cardviewOne' => $cardViewOne,
//            'cardviewTwo' => $cardViewTwo
//        ]);
//
//        $results =  $type === "JSON"
//            ? $qb->getQuery()->getScalarResult()
//            : $qb->getQuery()->getResult();
//
//        return (!empty($results))
//            ? $results
//            : [];
//    }
//
//

//
//
//    /**
//     * @param $groupNameID
//     * @param $userID
//     * @param null $type
//     * @return array|mixed|null
//     */
//    public function getAnalogCardReadings($groupNameID, $userID, $type = null)
//    {
//        $qb = $this->createQueryBuilder('cv');
//        $qb->select('a', 'r.room', 'i.iconname', 's.sensorname', 'cc.colour', 'cv.cardviewid')
//            ->leftJoin('App\Entity\Sensors\Analog', 'a', Join::WITH,'a.sensornameid = cv.sensornameid')
//            ->innerJoin('App\Entity\Core\Room', 'r', Join::WITH,'r.roomid = cv.roomid')
//            ->innerJoin('App\Entity\Core\Icons', 'i', Join::WITH,'i.iconid = cv.cardiconid')
//            ->innerJoin('App\Entity\Card\CardColour', 'cc', Join::WITH,'cc.colourid = cv.cardcolourid')
//            ->innerJoin('App\Entity\Core\Sensors', 's', Join::WITH,'s.sensornameid = cv.sensornameid')
//            ->where(
//                $qb->expr()->orX(
//                    $qb->expr()->eq('cv.cardstateid', ':cardviewOne'),
//                    $qb->expr()->eq('cv.cardstateid', ':cardviewTwo'),
//                ),
//                $qb->expr()->eq('cv.userid', ':userid'),
//                $qb->expr()->in('s.groupnameid', ':groupNameID')
//            )
//            ->setParameters(['userid' => $userID, 'groupNameID' => $groupNameID, 'cardviewOne' => Cardstate::ON, 'cardviewTwo' => Cardstate::INDEX_ONLY]);
//
//        $results =  $type === "JSON"
//            ? $qb->getQuery()->getScalarResult()
//            : $qb->getQuery()->getResult();
//
//        return (!empty($results))
//            ? $results
//            : [];
//    }
//
//    /**
//     * @param $groupNameID
//     * @param $userID
//     * @param null $type
//     * @return array|mixed|null
//     */
//    public function getTempCardReadings($groupNameID, $userID, $type = null): array
//    {
//        $qb = $this->createQueryBuilder('cv');
//        $qb->select('t', 'r.room', 'i.iconname', 's.sensorname', 'cc.colour', 'cv.cardviewid')
//            ->innerJoin('App\Entity\Sensors\Temp', 't', Join::WITH,'t.sensornameid = cv.sensornameid')
//            ->innerJoin('App\Entity\Core\Room', 'r', Join::WITH,'r.roomid = cv.roomid')
//            ->innerJoin('App\Entity\Core\Icons', 'i', Join::WITH,'i.iconid = cv.cardiconid')
//            ->innerJoin('App\Entity\Card\CardColour', 'cc', Join::WITH,'cc.colourid = cv.cardcolourid')
//            ->innerJoin('App\Entity\Core\Sensors', 's', Join::WITH,'s.sensornameid = cv.sensornameid')
//            ->where(
//                $qb->expr()->orX(
//                    $qb->expr()->eq('cv.cardstateid', ':cardviewOne'),
//                    $qb->expr()->eq('cv.cardstateid', ':cardviewTwo'),
//                ),
//                $qb->expr()->eq('cv.userid', ':userid'),
//                $qb->expr()->in('s.groupnameid', ':groupNameID')
//            )
//            ->setParameters(['userid' => $userID, 'groupNameID' => $groupNameID, 'cardviewOne' => 1, 'cardviewTwo' => 6]);
//
//        $results =  $type === "JSON"
//            ? $qb->getQuery()->getScalarResult()
//            : $qb->getQuery()->getResult();
//
//        return (!empty($results))
//            ? $results
//            : [];
//    }
//
//    /**
//     * @param $groupNameID
//     * @param $userID
//     * @param null $type
//     * @return array|mixed|null
//     */
//    public function getHumidCardReadings($groupNameID, $userID, $type = null): array
//    {
//        $qb = $this->createQueryBuilder('cv');
//        $qb->select( 'h', 'r.room', 'i.iconname', 's.sensorname', 'cc.colour', 'cv.cardviewid')
//            ->innerJoin('App\Entity\Sensors\Temp', 't', Join::WITH,'t.sensornameid = cv.sensornameid')
//            ->innerJoin('App\Entity\Core\Room', 'r', Join::WITH,'r.roomid = cv.roomid')
//            ->innerJoin('App\Entity\Core\Icons', 'i', Join::WITH,'i.iconid = cv.cardiconid')
//            ->innerJoin('App\Entity\Card\CardColour', 'cc', Join::WITH,'cc.colourid = cv.cardcolourid')
//            ->innerJoin('App\Entity\Core\Sensors', 's', Join::WITH,'s.sensornameid = cv.sensornameid')
//            ->where(
//                $qb->expr()->orX(
//                    $qb->expr()->eq('cv.cardstateid', ':cardviewOne'),
//                    $qb->expr()->eq('cv.cardstateid', ':cardviewTwo'),
//                ),
//                $qb->expr()->eq('cv.userid', ':userid'),
//                $qb->expr()->in('s.groupnameid', ':groupNameID')
//            )
//            ->setParameters(['userid' => $userID, 'groupNameID' => $groupNameID, 'cardviewOne' => 1, 'cardviewTwo' => 6]);
//
//        $results =  $type === "JSON"
//            ? $qb->getQuery()->getScalarResult()
//            : $qb->getQuery()->getResult();
//
//        return (!empty($results))
//            ? $results
//            : [];
//    }
//
//
//





}
