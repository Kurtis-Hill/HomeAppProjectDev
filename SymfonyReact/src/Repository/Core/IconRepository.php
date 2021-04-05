<?php


namespace App\Repository\Core;


use App\Entity\Card\CardColour;
use App\Entity\Core\Icons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

class IconRepository extends EntityRepository
{
   public function getAllIcons()
   {
       $qb = $this->createQueryBuilder('i')
           ->orderBy('i.iconName', 'ASC');

       return $qb->getQuery()->getArrayResult();
   }

   public function countAllIcons()
   {
       return $this->createQueryBuilder('icons')
           ->select('count(icons.iconID)')
           ->getQuery()->getSingleScalarResult();
   }


}
