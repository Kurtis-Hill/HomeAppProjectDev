<?php

namespace App\Repository\Card;

use App\Entity\Card\Cardcolour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Cardcolour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cardcolour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cardcolour[]    findAll()
 * @method Cardcolour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardcolourRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cardcolour::class);
    }

    public function getAllColours()
    {
        $qb = $this->createQueryBuilder('c')
              ->orderBy('c.colour', 'ASC');

        $result = $qb->getQuery()->getScalarResult();
        return $result;
    }

    // /**
    //  * @return Cardcolour[] Returns an array of Cardcolour objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cardcolour
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
