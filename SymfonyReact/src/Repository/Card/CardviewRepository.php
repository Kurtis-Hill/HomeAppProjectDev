<?php


namespace App\Repository\Card;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use function Doctrine\ORM\QueryBuilder;

class CardviewRepository extends EntityRepository
{
    /**
     * @param $groupNameID
     * @param $userID
     * @param null $type
     * @return array|mixed

     */

    public function getAllCardReadings($groupNameID, $userID, $type = null, $order = null)
    {
       // dd($room);
        $qb = $this->createQueryBuilder('cv');
         $qb->select('t', 'h', 'a', 'r.room', 'i.iconname', 's.sensorname', 'cc.colour', 'cv.cardviewid')
            ->leftJoin('App\Entity\Sensors\Temp', 't', Join::WITH,'t.sensornameid = cv.sensornameid')
            ->leftJoin('App\Entity\Sensors\Humid', 'h', Join::WITH,'h.sensornameid = cv.sensornameid')
            ->leftJoin('App\Entity\Sensors\Analog', 'a', Join::WITH,'a.sensornameid = cv.sensornameid')
            ->innerJoin('App\Entity\Core\Room', 'r', Join::WITH,'r.roomid = cv.roomid')
            ->innerJoin('App\Entity\Core\Icons', 'i', Join::WITH,'i.iconid = cv.cardiconid')
            ->innerJoin('App\Entity\Card\Cardcolour', 'cc', Join::WITH,'cc.colourid = cv.cardcolourid')
            ->innerJoin('App\Entity\Core\Sensornames', 's', Join::WITH,'s.sensornameid = cv.sensornameid');
         if ($order = null) {
//             $qb->orderBy('r.roomname', 'ASC');
//             $qb->addOrderBy('t.tempid')
//                 ->addOrderBy('h.humidid')
//                 ->addOrderBy('a.analogid');
         }
         $qb->where(
             $qb->expr()->orX(
                 $qb->expr()->eq('cv.cardstateid', ':cardviewOne'),
                 $qb->expr()->eq('cv.cardstateid', ':cardviewTwo')
             ),
             $qb->expr()->eq('cv.userid', ':userid'),
             $qb->expr()->eq('s.groupnameid', ':groupNameID')
         )
             ->setParameters(['userid' => $userID, 'groupNameID' => $groupNameID, 'cardviewOne' => 1, 'cardviewTwo' => 6])
             ;



         $result = null;
         if($type === "JSON") {
             $result = $qb->getQuery()->getScalarResult();
            // dd($result);
         }
         else {
             $result = $qb->getQuery()->getResult();
         }
        return $result;
    }

    public function getAnalogCardReadings($groupNameID, $userID, $type = null)
    {
        $qb = $this->createQueryBuilder('cv');
        $qb->select('a', 'r.room', 'i.iconname', 's.sensorname', 'cc.colour', 'cv.cardviewid')
            ->leftJoin('App\Entity\Sensors\Analog', 'a', Join::WITH,'a.sensornameid = cv.sensornameid')
            ->innerJoin('App\Entity\Core\Room', 'r', Join::WITH,'r.roomid = cv.roomid')
            ->innerJoin('App\Entity\Core\Icons', 'i', Join::WITH,'i.iconid = cv.cardiconid')
            ->innerJoin('App\Entity\Card\Cardcolour', 'cc', Join::WITH,'cc.colourid = cv.cardcolourid')
            ->innerJoin('App\Entity\Core\Sensornames', 's', Join::WITH,'s.sensornameid = cv.sensornameid')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->eq('cv.cardstateid', ':cardviewOne'),
                    $qb->expr()->eq('cv.cardstateid', ':cardviewTwo'),
                ),
                $qb->expr()->eq('cv.userid', ':userid'),
                $qb->expr()->eq('s.groupnameid', ':groupNameID')
            )
            ->setParameters(['userid' => $userID, 'groupNameID' => $groupNameID, 'cardviewOne' => 1, 'cardviewTwo' => 6]);

        $result = null;
        if($type === "json") {
            $result = $qb->getQuery()->getScalarResult();
        }
        else {
            $result = $qb->getQuery()->getResult();
        }
        return $result;
    }

    public function getTempCardReadings($groupNameID, $userID, $type = null)
    {
        $qb = $this->createQueryBuilder('cv');
        $qb->select('t', 'r.room', 'i.iconname', 's.sensorname', 'cc.colour', 'cv.cardviewid')
            ->innerJoin('App\Entity\Sensors\Temp', 't', Join::WITH,'t.sensornameid = cv.sensornameid')
            ->innerJoin('App\Entity\Core\Room', 'r', Join::WITH,'r.roomid = cv.roomid')
            ->innerJoin('App\Entity\Core\Icons', 'i', Join::WITH,'i.iconid = cv.cardiconid')
            ->innerJoin('App\Entity\Card\Cardcolour', 'cc', Join::WITH,'cc.colourid = cv.cardcolourid')
            ->innerJoin('App\Entity\Core\Sensornames', 's', Join::WITH,'s.sensornameid = cv.sensornameid')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->eq('cv.cardstateid', ':cardviewOne'),
                    $qb->expr()->eq('cv.cardstateid', ':cardviewTwo'),
                ),
                $qb->expr()->eq('cv.userid', ':userid'),
                $qb->expr()->eq('s.groupnameid', ':groupNameID')
            )
            ->setParameters(['userid' => $userID, 'groupNameID' => $groupNameID, 'cardviewOne' => 1, 'cardviewTwo' => 6]);

        $result = null;
        if($type === "json") {
            $result = $qb->getQuery()->getScalarResult();
        }
        else {
            $result = $qb->getQuery()->getResult();
        }

        return $result;
    }

    public function getHumidCardReadings($groupNameID, $userID, $type = null)
    {
        $qb = $this->createQueryBuilder('cv');
        $qb->select( 'h', 'r.room', 'i.iconname', 's.sensorname', 'cc.colour', 'cv.cardviewid')
            ->innerJoin('App\Entity\Sensors\Temp', 't', Join::WITH,'t.sensornameid = cv.sensornameid')
            ->innerJoin('App\Entity\Core\Room', 'r', Join::WITH,'r.roomid = cv.roomid')
            ->innerJoin('App\Entity\Core\Icons', 'i', Join::WITH,'i.iconid = cv.cardiconid')
            ->innerJoin('App\Entity\Card\Cardcolour', 'cc', Join::WITH,'cc.colourid = cv.cardcolourid')
            ->innerJoin('App\Entity\Core\Sensornames', 's', Join::WITH,'s.sensornameid = cv.sensornameid')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->eq('cv.cardstateid', ':cardviewOne'),
                    $qb->expr()->eq('cv.cardstateid', ':cardviewTwo'),
                ),
                $qb->expr()->eq('cv.userid', ':userid'),
                $qb->expr()->eq('s.groupnameid', ':groupNameID')
            )
            ->setParameters(['userid' => $userID, 'groupNameID' => $groupNameID, 'cardviewOne' => 1, 'cardviewTwo' => 6]);

        $result = null;
        if($type === "JSON") {
            $result = $qb->getQuery()->getScalarResult();
        }
        if($type === "Object") {
            $result = $qb->getQuery()->getResult();
        }

        return $result;
    }


    //Add left join for additional sensors
    public function getCardFormData(array $criteria)
    {
        $qb = $this->createQueryBuilder('cv');
        $qb->select('cv', 't', 'h', 'a', 'i', 'cc', 's', 'cs', 'st')
            ->leftJoin('App\Entity\Sensors\Temp', 't', Join::WITH,'t.sensornameid = cv.sensornameid')
            ->leftJoin('App\Entity\Sensors\Humid', 'h', Join::WITH,'h.sensornameid = cv.sensornameid')
            ->leftJoin('App\Entity\Sensors\Analog', 'a', Join::WITH,'a.sensornameid = cv.sensornameid')
            ->innerJoin('App\Entity\Core\Icons', 'i', Join::WITH,'i.iconid = cv.cardiconid')
            ->innerJoin('App\Entity\Card\Cardcolour', 'cc', Join::WITH,'cc.colourid = cv.cardcolourid')
            ->innerJoin('App\Entity\Core\Sensornames', 's', Join::WITH,'s.sensornameid = cv.sensornameid')
            ->innerJoin('App\Entity\Card\Cardstate', 'cs', Join::WITH,'cs.cardstateid = cv.cardstateid')
            ->innerJoin('App\Entity\Core\Sensortype', 'st', Join::WITH,'s.sensortypeid = st.sensortypeid')
            ->where(
                $qb->expr()->eq('cv.cardviewid', ':id')
            )
            ->setParameters(['id' => $criteria['id']]);
        $result = $qb->getQuery()->getScalarResult();

        return $result[0];
    }

    public function getUsersCurrentCardData(array $criteria)
    {
        $qb = $this->createQueryBuilder('cv');
        $qb->select('cv', 't', 'h', 'a')
            ->leftJoin('App\Entity\Sensors\Temp', 't', Join::WITH,'t.sensornameid = cv.sensornameid')
            ->leftJoin('App\Entity\Sensors\Humid', 'h', Join::WITH,'h.sensornameid = cv.sensornameid')
            ->leftJoin('App\Entity\Sensors\Analog', 'a', Join::WITH,'a.sensornameid = cv.sensornameid')
            ->where(
                $qb->expr()->eq('cv.cardviewid', ':id'),
                $qb->expr()->eq('cv.userid', ':userid')
            )
            ->setParameters(['id' => $criteria['id'], 'userid' => $criteria['userID']]);

        $result = $qb->getQuery()->getResult();
        //dd($result);

        $sensorResults["cardView"] = $result[0];
        $sensorResults["temp"] = $result[1];
        $sensorResults["humid"] = $result[2];
        $sensorResults["analog"] = $result[3];


        return $sensorResults;
    }

//    public function getTempHumidCardViewData(array $criteria)
//    {
//        $qb = $this->createQueryBuilder('cv');
//        $qb->select('cv', 't', 'h')
//        ->leftJoin('App\Entity\Sensors\Temp', 't', Join::WITH,'t.sensornameid = cv.sensornameid')
//        ->leftJoin('App\Entity\Sensors\Humid', 'h', Join::WITH,'h.sensornameid = cv.sensornameid')
//    }



}