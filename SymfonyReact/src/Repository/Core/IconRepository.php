<?php


namespace App\Repository\Core;


use App\UserInterface\Entity\Icons;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

class IconRepository extends EntityRepository
{
   public function getAllIcons(): array
   {
       $qb = $this->createQueryBuilder('i')
           ->orderBy('i.iconName', 'ASC');

       return $qb->getQuery()->getArrayResult();
   }

   public function countAllIcons(): int
   {
       return (int) $this->createQueryBuilder('icons')
           ->select('count(icons.iconID)')
           ->getQuery()->getSingleScalarResult();
   }

   public function getFirstIconId(): Icons
   {
       return $this->createQueryBuilder('icons')
           ->select()
           ->orderBy('icons.iconID', 'ASC')
           ->setMaxResults(1)
           ->getQuery()->getOneOrNullResult();
   }


}
