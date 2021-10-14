<?php


namespace App\Repository\Sensors;


use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use Doctrine\ORM\EntityRepository;

class TempRepository extends EntityRepository
{
    public function findOneById(int $id): ?Temperature
    {
        $qb = $this->createQueryBuilder('s');
        $expr = $qb->expr();

        $qb->select()
//            ->where(
//                $expr->eq('s.sensorNameID', ':sensorNameID')
//            )
//            ->setParameter('sensorNameID', $id)
        ;
dd($qb->getQuery()->getResult(), $id);
        return $qb->getQuery()->getOneOrNullResult();
    }
}
