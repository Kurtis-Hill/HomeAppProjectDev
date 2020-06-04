<?php


namespace App\Repository\Core;


use App\Entity\Card\Cardcolour;
use App\Entity\Core\Icons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class IconRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Icons::class);
    }

   public function getAllIcons()
   {
       $qb = $this->createQueryBuilder('i')
           ->orderBy('i.iconname', 'ASC');

       $result = $qb->getQuery()->getScalarResult();
       //dd($result);
       return $result;
   }


}