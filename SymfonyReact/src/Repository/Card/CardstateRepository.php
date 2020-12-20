<?php


namespace App\Repository\Card;


use App\Entity\Card\Cardstate;
use App\Entity\Core\Icons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

class CardstateRepository extends EntityRepository
{
    public function getAllCardStates()
    {
        $qb = $this->createQueryBuilder('cs');

        $result = $qb->getQuery()->getScalarResult();

        return $result;
    }

    public function getAllStates()
    {
        $qb = $this->createQueryBuilder('cs')
            ->orderBy('cs.cardstateid', 'ASC');

        return $qb->getQuery()->getScalarResult();
    }
}