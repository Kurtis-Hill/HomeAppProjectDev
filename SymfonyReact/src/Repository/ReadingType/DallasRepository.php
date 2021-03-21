<?php


namespace App\Repository\ReadingType;


use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorTypes\Dallas;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

class DallasRepository extends EntityRepository
{
    /**
     * @param Sensors $sensor
     * @return Dallas|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findDallasSensor(Sensors $sensor): ?Temperature
    {
        $qb = $this->createQueryBuilder('d');
        $expr = $qb->expr();

        $qb->select('t')
            ->innerJoin(Temperature::class, 't', Join::WITH, 't.tempID = d.tempID')
            ->innerJoin(Sensors::class, 's', Join::WITH, 's.sensorNameID = t.sensorNameID')
            ->where(
                $expr->eq('d.sensor', ':sensorObject')
            )
            ->setParameters(
                [
                    'sensorObject' => $sensor
                ]
            );

        return $qb->getQuery()->getOneOrNullResult();
    }
}
