<?php


namespace App\Repository\Sensors;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

class TempRepository extends EntityRepository
{
    public function getTempCardReadings($type, $groupName, $id)
    {
     //   dd($groupName);
        $qb = $this->createQueryBuilder('t');

        $qb->select('t', 'cstate.state', 'sn.sensorname', 'i.iconname', 'cc.colour', 'u.userid', 'r.room', 'cstate.state')
            ->innerJoin('App\Entity\Card\Cardshow', 'cshow', Join::WITH, 't.cardshowid = cshow.cardshowid')
            ->innerJoin('App\Entity\Card\Cardstate', 'cstate', Join::WITH, 'cstate.cardstateid = cshow.indexpage')
            ->innerJoin('App\Entity\Card\Cardview', 'cv', Join::WITH, 'cv.cardviewid = t.cardviewid')
            ->innerJoin('App\Entity\Core\Icons', 'i', Join::WITH, 'i.iconid = cv.cardiconid')
            ->innerJoin('App\Entity\Core\GroupName', 'gn', Join::WITH, 'gn.groupnameid = t.groupnameid')
            ->innerJoin('App\Entity\Core\Sensornames', 'sn', Join::WITH, 'sn.sensornameid = t.sensornameid')
            ->innerJoin('App\Entity\Card\Cardcolour', 'cc', Join::WITH, 'cv.cardcolourid = cc.colourid')
            ->innerJoin('App\Entity\Core\User', 'u', Join::WITH, 'u.groupnameid = gn.groupnameid')
            ->innerJoin('App\Entity\Core\Room', 'r', Join::WITH, 'r.roomid = t.roomid')
           // ->innerJoin('App\Entity\Card\Cardstate', 'c', Join::WITH, 't.sendornameid = h.sensornameid')
            ->where(
                $qb->expr()->eq('u.userid', ':userid'),
                $qb->expr()->in('t.groupnameid', ':groupname'),
                $qb->expr()->neq('cshow.cardshowid', ':notshown'),
            )

            ->setParameters([':notshown' => 2, ':groupname' => $groupName, 'userid' => $id])
            ;

        return $type === "JSON"
            ? $qb->getQuery()->getScalarResult()
            : $qb->getQuery()->getResult();
    }

}