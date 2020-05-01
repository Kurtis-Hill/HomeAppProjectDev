<?php


namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class RoomRepository extends EntityRepository
{
    public function getRoomsForUser($groupNameid)
    {
        $qb = $this->createQueryBuilder('r');

        $qb->select('r')
            ->where(
                $qb->expr()->eq('r.groupnameid', ':groupnameid')
            )
            ->setParameter('groupnameid', $groupNameid);

        $result = $qb->getQuery()->getScalarResult();
       // dd($result);
        return $result;
    }

}
//* @ORM\Entity(repositoryClass="App\Repository\RoomRepository")