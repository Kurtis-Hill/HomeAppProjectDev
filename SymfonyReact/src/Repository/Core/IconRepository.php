<?php


namespace App\Repository\Core;


use App\Entity\Card\Cardcolour;
use App\Entity\Core\Icons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

class IconRepository extends EntityRepository
{
   public function getAllIcons()
   {
       $qb = $this->createQueryBuilder('i')
           ->orderBy('i.iconname', 'ASC');

       $result = $qb->getQuery()->getScalarResult();

       return $result;
   }


}