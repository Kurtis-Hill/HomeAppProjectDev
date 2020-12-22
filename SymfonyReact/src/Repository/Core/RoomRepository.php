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

        $qb->select('r.roomID, r.room')
            ->where(
                $qb->expr()->in('r.groupNameID', ':groupNameID')
            )
            ->setParameter('groupNameID', $groupNameid);

        return $qb->getQuery()->getArrayResult();
    }

}
