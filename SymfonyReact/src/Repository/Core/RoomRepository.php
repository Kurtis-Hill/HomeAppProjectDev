<?php


namespace App\Repository\Core;

use Doctrine\ORM\EntityRepository;

class RoomRepository extends EntityRepository
{
    /**
     * @param $groupNameid
     * @return array
     */
    public function getRoomsForUser($groupNameid): array
    {
        $qb = $this->createQueryBuilder('r');

        $qb->select('r.roomid, r.room')
            ->where(
                $qb->expr()->in('r.groupnameid', ':groupnameid')
            )
            ->setParameter('groupnameid', $groupNameid);
//dd($qb->getQuery()->getArrayResult());
        return $qb->getQuery()->getArrayResult();
    }

}
