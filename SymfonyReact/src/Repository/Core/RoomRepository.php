<?php


namespace App\Repository\Core;

use App\Entity\Core\GroupNames;
use Doctrine\ORM\EntityRepository;

class RoomRepository extends EntityRepository
{
    /**
     * @param $groupNameid
     * @return array
     */
    public function getAllUserRoomsByGroupId($groupNameid): array
    {
        $qb = $this->createQueryBuilder('r');

        $qb->select('r.roomID, r.room')
            ->where(
                $qb->expr()->in('r.groupNameID', ':groupNameID')
            )
            ->setParameter('groupNameID', $groupNameid);

        return $qb->getQuery()->getArrayResult();
    }

    public function findRoomByGroupNameAndName(GroupNames $groupName, string $roomName)
    {
        $qb = $this->createQueryBuilder('r');
        $expr = $qb->expr();

        $qb->select('r')
            ->where(
                $expr->eq('r.room' ,':room')
            )
            ->setParameters([
                'room' => $roomName
            ]);

        return $qb->getQuery()->getOneOrNullResult();
    }

}
