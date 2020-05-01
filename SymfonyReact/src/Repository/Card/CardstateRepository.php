<?php


namespace App\Repository\Card;


use App\Entity\Card\Cardstate;
use App\Entity\Core\Icons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class CardstateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cardstate::class);
    }

    public function getAllCardStates()
    {
        $qb = $this->createQueryBuilder('cs');

        $result = $qb->getQuery()->getScalarResult();

        return $result;
    }
}