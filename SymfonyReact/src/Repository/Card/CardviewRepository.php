<?php


namespace App\Repository\Card;


use App\Entity\Card\Cardstate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use function Doctrine\ORM\QueryBuilder;

//All queries need to be refactored to just grab the data needed just passed full objects in for convenience
class CardviewRepository extends EntityRepository
{

    public function getAllIndexCardObjects(int $userID, array $groupNameIDs)
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

            ->innerJoin('App\Entity\Sensors\Sensors', 'sensors', Join::WITH, 'cv.sensorNameID = sensors.sensorNameID')
            ->innerJoin('App\Entity\Sensors\Devices', 'devices', Join::WITH, 'sensors.deviceNameID = devices.deviceNameID')
            ->leftJoin('App\Entity\Sensors\SensorTypes\Dht', 'dht', Join::WITH,'cv.cardViewID = dht.cardViewID')
            ->leftJoin('App\Entity\Sensors\SensorTypes\Dallas', 'dallas', Join::WITH,'cv.cardViewID = dallas.cardViewID')
            ->leftJoin('App\Entity\Sensors\SensorTypes\Soil', 'soil', Join::WITH,'cv.cardViewID = soil.cardViewID')
            ->leftJoin('App\Entity\Sensors\SensorTypes\Bmp', 'bmp', Join::WITH,'cv.cardViewID = bmp.cardViewID')
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
//dd( array_filter($qb->getQuery()->getResult()));
        return array_filter($qb->getQuery()->getResult());
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
//    /**
//     * @param array $groupNameIDs
//     * @param int $userID
//     * @param null $type
//     * @param $deviceDetails
//     * @return array
//     */
//    public function getAllCardReadingsForDevice(array $groupNameIDs, int $userID, $deviceDetails, $type = null): array
//    {
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
//        ]);
//
//        $results =  $type === "JSON"
//            ? $qb->getQuery()->getScalarResult()
//            : $qb->getQuery()->getResult();
//
////        dd('results', $deviceDetails);
//        return (!empty($results))
//            ? $results
//            : [];
//    }
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
//    /**
//     * Add left join for additional sensors
//     * needs refactor
//     * @param array $criteria
//     * @return mixed
//     */
//    public function getCardFormData(array $criteria): array
//    {
//        $qb = $this->createQueryBuilder('cv');
//        $qb->select('t', 'h', 'a', 'cv', 'i', 'cc', 's', 'cs', 'st')
//            ->leftJoin('App\Entity\Sensors\Temp', 't', Join::WITH,'t.sensornameid = cv.sensornameid')
//            ->leftJoin('App\Entity\Sensors\Humid', 'h', Join::WITH,'h.sensornameid = cv.sensornameid')
//            ->leftJoin('App\Entity\Sensors\Analog', 'a', Join::WITH,'a.sensornameid = cv.sensornameid')
//            ->innerJoin('App\Entity\Core\Icons', 'i', Join::WITH,'i.iconid = cv.cardiconid')
//            ->innerJoin('App\Entity\Card\CardColour', 'cc', Join::WITH,'cc.colourid = cv.cardcolourid')
//            ->innerJoin('App\Entity\Core\Sensors', 's', Join::WITH,'s.sensornameid = cv.sensornameid')
//            ->innerJoin('App\Entity\Card\Cardstate', 'cs', Join::WITH,'cs.cardstateid = cv.cardstateid')
//            ->innerJoin('App\Entity\Core\Sensortype', 'st', Join::WITH,'s.sensortypeid = st.sensortypeid')
//            ->where(
//                $qb->expr()->eq('cv.cardviewid', ':id')
//            )
//            ->setParameters(['id' => $criteria['id']]);
//
//        $result = $qb->getQuery()->getResult();
//
//        $returnedResult = [];
//        if (!empty($result)) {
//            $returnedResult['temp'] = $result['1'];
//            $returnedResult['humid'] = $result['2'];
//            $returnedResult['analog'] = $result['3'];
//            $returnedResult['icons'] = $result['4'];
//            $returnedResult['cardColour'] = $result['5'];
//            $returnedResult['sensorNames'] = $result['6'];
//            $returnedResult['cardState'] = $result['7'];
//            $returnedResult['sensorType'] = $result['8'];
//        }
//
//        return $returnedResult;
//    }
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
//



}
