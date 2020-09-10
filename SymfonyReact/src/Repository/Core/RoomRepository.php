<?php


namespace App\Repository\Core;

use Doctrine\ORM\EntityRepository;

class RoomRepository extends EntityRepository
{
    public function getRoomsForUser($groupNameid)
    {
        $qb = $this->createQueryBuilder('r');

        $qb->select('r')
            ->where(
                $qb->expr()->in('r.groupnameid', ':groupnameid')
            )
            ->setParameter('groupnameid', $groupNameid);

        $result = $qb->getQuery()->getScalarResult();
       // dd($result);
        return $result;
    }

}
//* @ORM\Entity(repositoryClass="App\Repository\RoomRepository")