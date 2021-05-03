<?php


namespace App\Repository\Card;


use App\Entity\Card\Cardstate;
use App\Entity\Core\Icons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

class CardstateRepository extends EntityRepository
{
    public function getAllStates()
    {
        $qb = $this->createQueryBuilder('cs')
            ->orderBy('cs.cardStateID', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }

    public function getFirstStateId()
    {
        return $this->createQueryBuilder('state')
            ->select()
            ->orderBy('state.cardStateID', 'ASC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    public function countAllStates()
    {
        return $this->createQueryBuilder('state')
            ->select('count(state.cardStateID)')
            ->getQuery()->getSingleScalarResult();
    }
}
