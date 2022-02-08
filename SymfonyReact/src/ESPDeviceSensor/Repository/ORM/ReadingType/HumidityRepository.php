<?php

namespace App\ESPDeviceSensor\Repository\ORM\ReadingType;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

class HumidityRepository extends ServiceEntityRepository implements ReadingTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Humidity::class);
    }

    public function persist(AllSensorReadingTypeInterface $readingTypeObject): void
    {
        $this->getEntityManager()->persist($readingTypeObject);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findOneById(int $id)
    {
        return $this->findOneBy(['humidID' => $id]);
    }


    public function getOneBySensorNameID(int $sensorNameID): ?Humidity
    {
        $qb = $this->createQueryBuilder(Humidity::READING_TYPE);
        $expr = $qb->expr();

        $qb->select(Humidity::READING_TYPE)
            ->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, Humidity::READING_TYPE.'.sensorNameID = '.Sensor::ALIAS.'.sensorNameID')
            ->where(
                $expr->eq(
                    Sensor::ALIAS.'.sensorNameID',
                    ':sensorNameID'
                )
            )
            ->setParameters(['sensorNameID' => $sensorNameID]);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
