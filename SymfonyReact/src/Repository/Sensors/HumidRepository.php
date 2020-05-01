<?php


namespace App\Repository\Sensors;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

class HumidRepository extends EntityRepository
{
    private function queryAllHumidReadings($type, $groupName, $id)
    {
        //   dd($groupName);
        $qb = $this->createQueryBuilder('h');

        $qb->select('h', 'cstate.state', 'sn.sensorname', 'i.iconname', 'cc.colour', 'u.userid', 'r.room')
            ->innerJoin('App\Entity\Card\Cardshow', 'cshow', Join::WITH, 'h.cardshowid = cshow.cardshowid')
            ->innerJoin('App\Entity\Card\Cardstate', 'cstate', Join::WITH, 'cstate.cardstateid = cshow.indexpage')
            ->innerJoin('App\Entity\Card\Cardview', 'cv', Join::WITH, 'cv.cardviewid = h.cardviewid')
            ->innerJoin('App\Entity\Core\Icons', 'i', Join::WITH, 'i.iconid = cv.cardiconid')
            ->innerJoin('App\Entity\Core\GroupName', 'gn', Join::WITH, 'gn.groupnameid = h.groupnameid')
            ->innerJoin('App\Entity\Core\Sensornames', 'sn', Join::WITH, 'sn.sensornameid = h.sensornameid')
            ->innerJoin('App\Entity\Card\Cardcolour', 'cc', Join::WITH, 'cv.cardcolourid = cc.colourid')
            ->innerJoin('App\Entity\Core\User', 'u', Join::WITH, 'u.groupnameid = gn.groupnameid')
            ->innerJoin('App\Entity\Core\Room', 'r', Join::WITH, 'r.roomid = h.roomid')
            ->where(
                $qb->expr()->eq('u.userid', ':userid'),
                $qb->expr()->eq('h.groupnameid', ':groupname'),
                $qb->expr()->neq('cshow.cardshowid', ':notshown'),
            )

            ->setParameters([':notshown' => 2, ':groupname' => $groupName, 'userid' => $id])
        ;

        if ($type === 'json') {
            $result = $qb->getQuery()->getScalarResult();
        }
        else {
            $result = $qb->getQuery()->getResult();
        }

       // dd($result);
        return $result;
    }

    public function getHumidCardReadings($type, $groupName, $id)
    {
        return $this->queryAllHumidReadings($type, $groupName, $id);
    }
}