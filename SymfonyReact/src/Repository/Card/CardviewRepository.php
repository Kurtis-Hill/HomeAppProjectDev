<?php


namespace App\Repository\Card;


use App\Entity\Card\CardColour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\Icons;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\SensorType;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use function Doctrine\ORM\QueryBuilder;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\Devices;
use App\Entity\Sensors\SensorTypes\Dht;
use App\Entity\Sensors\SensorTypes\Dallas;
use App\Entity\Sensors\SensorTypes\Soil;
use App\Entity\Sensors\SensorTypes\Bmp;

//All queries need to be refactored to just grab the data needed just passed full objects in for convenience
class CardviewRepository extends EntityRepository
{

    public function getAllCardObjects(int $userID, array $groupNameIDs)
    {
        $cardViewOne = Cardstate::ON;
        $cardViewTwo = Cardstate::INDEX_ONLY;

        $qb = $this->createQueryBuilder('cv');
        $expr = $qb->expr();

        $qb->select(
                    'dallas',
                    'dht',
                    'bmp',
                    'soil'
        )
            ->innerJoin(Sensors::class, 'sensors', Join::WITH, 'sensors.sensorNameID = cv.sensorNameID')
            ->innerJoin(Devices::class, 'devices', Join::WITH, 'sensors.deviceNameID = devices.deviceNameID')
            ->leftJoin(Dht::class, 'dht', Join::WITH,'dht.cardViewID = cv.cardViewID')
            ->leftJoin(Dallas::class, 'dallas', Join::WITH,'dallas.cardViewID = cv.cardViewID')
            ->leftJoin(Soil::class, 'soil', Join::WITH,'soil.cardViewID = cv.cardViewID')
            ->leftJoin(Bmp::class, 'bmp', Join::WITH,'bmp.cardViewID = cv.cardViewID')
            ->where(
                $expr->orX(
                    $expr->eq('cv.cardStateID', ':cardviewOne'),
                    $expr->eq('cv.cardStateID', ':cardviewTwo')
                ),
                $expr->eq('cv.userID', ':userid'),
                $expr->in('devices.groupNameID', ':groupNameID')
            );

        $qb->setParameters(
            [
                'userid' => $userID,
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
     * @param null $type
     * @param $deviceDetails
     * @return array
     */
    public function getAllCardReadingsForDevice(array $groupNameIDs, int $userID, $deviceDetails, $type = null): array
    {
        $qb = $this->createQueryBuilder('cv');
        $qb->select('dallas', 'dht', 'bmp', 'soil')
            ->innerJoin(Sensors::class, 'sensors', Join::WITH, 'sensors.sensorNameID = cv.sensorNameID')
            ->innerJoin(Devices::class, 'devices', Join::WITH, 'sensors.deviceNameID = devices.deviceNameID')
            ->leftJoin(Dht::class, 'dht', Join::WITH,'dht.cardViewID =cv.cardViewID')
            ->leftJoin(Dallas::class, 'dallas', Join::WITH,'dallas.cardViewID =cv.cardViewID')
            ->leftJoin(Soil::class, 'soil', Join::WITH,'soil.cardViewID =cv.cardViewID')
            ->leftJoin(Bmp::class, 'bmp', Join::WITH,'bmp.cardViewID =cv.cardViewID')
            ->where(
            $qb->expr()->in('s.groupNameID', ':groupNameID'),
            $qb->expr()->eq('cv.userID', ':userID'),
            $qb->expr()->eq('s.deviceNameID', ':deviceNameID'),
            $qb->expr()->eq('s.groupNameID', ':deviceGroup'),
            $qb->expr()->eq('cv.roomID', ':deviceRoom')
        );
        $qb->setParameters([
            'deviceNameID' => $deviceDetails['deviceName'],
            'deviceGroup' => $deviceDetails['deviceGroup'],
            'deviceRoom' => $deviceDetails['deviceRoom'],
            'userID' => $userID,
            'groupNameID' => $groupNameIDs,
        ]);
//dd()
        return array_filter($qb->getQuery()->getResult());
    }




    /**
     * Add left join for additional sensors
     * needs refactor
     * @param array $criteria
     * @return mixed
     */
    public function getCardSensorFormData(array $criteria): StandardSensorTypeInterface
    {
        $qb = $this->createQueryBuilder('cv');
        $qb->select('dallas', 'dht', 'bmp', 'soil')
            ->innerJoin(Icons::class, 'i', Join::WITH,'i.iconID = cv.cardIconID')
            ->innerJoin(CardColour::class, 'cc', Join::WITH,'cc.colourID = cv.cardColourID')
            ->innerJoin(Sensors::class, 's', Join::WITH,'s.sensorNameID = cv.sensorNameID')
            ->innerJoin(Cardstate::class, 'cs', Join::WITH,'cs.cardStateID = cv.cardStateID')
            ->innerJoin(SensorType::class, 'st', Join::WITH,'s.sensorTypeID = st.sensorTypeID')
            ->leftJoin(Dht::class, 'dht', Join::WITH,'dht.cardViewID = cv.cardViewID')
            ->leftJoin(Dallas::class, 'dallas', Join::WITH,'dallas.cardViewID =cv.cardViewID')
            ->leftJoin(Soil::class, 'soil', Join::WITH,'soil.cardViewID =cv.cardViewID')
            ->leftJoin(Bmp::class, 'bmp', Join::WITH,'bmp.cardViewID =cv.cardViewID')
            ->where(
                $qb->expr()->eq('cv.cardViewID', ':id')
            )
            ->setParameters(['id' => $criteria['id']]);

        $result = array_filter($qb->getQuery()->getResult());

        $result = array_values($result);

        return $result[0];
    }


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
//    /**
//     * @param array $criteria
//     * @return mixed
//     */
//    public function getUsersCurrentCardData(array $criteria): array
//    {
//        $qb = $this->createQueryBuilder('cv');
//        $qb->select('cv', 't', 'h', 'a')
//            ->leftJoin('App\Entity\Sensors\Temp', 't', Join::WITH,'t.sensornameid = cv.sensornameid')
//            ->leftJoin('App\Entity\Sensors\Humid', 'h', Join::WITH,'h.sensornameid = cv.sensornameid')
//            ->leftJoin('App\Entity\Sensors\Analog', 'a', Join::WITH,'a.sensornameid = cv.sensornameid')
//            ->where(
//                $qb->expr()->eq('cv.cardviewid', ':id'),
//                $qb->expr()->eq('cv.userid', ':userid')
//            )
//            ->setParameters(['id' => $criteria['id'], 'userid' => $criteria['userID']]);
//
//        $result = $qb->getQuery()->getResult();
//
//        $sensorResults["cardViewObject"] = $result[0];
//        $sensorResults["temp"] = $result[1];
//        $sensorResults["humid"] = $result[2];
//        $sensorResults["analog"] = $result[3];
//
//
//        return $sensorResults;
//    }




}
