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

//       dd($qb->getQuery()->getArrayResult());
       return $qb->getQuery()->getArrayResult();
   }


}
