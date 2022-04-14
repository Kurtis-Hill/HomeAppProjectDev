<?php

namespace App\Sensors\Repository\ORM\ReadingType;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

class TemperatureRepository extends ServiceEntityRepository implements ReadingTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Temperature::class);
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
        return $this->findOneBy(['tempID' => $id]);
    }

    public function findAllBySensorName(string $name): array
    {
        $qb = $this->createQueryBuilder(Temperature::READING_TYPE);
        $expr = $qb->expr();

        $qb->select(Temperature::READING_TYPE)
            ->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, Temperature::READING_TYPE.'.sensorNameID = '.Sensor::ALIAS.'.sensorNameID')
            ->where(
                $expr->eq(
                    Sensor::ALIAS.'.sensorName',
                    ':sensorName'
                )
            )
            ->setParameters(['sensorName' => $name]);

        return $qb->getQuery()->getResult() ?? [];
    }

    public function removeObject(AllSensorReadingTypeInterface $readingTypeObject)
    {
//        $this->getEntityManager()->detach($readingTypeObject);
    }

    public function getOneBySensorNameID(int $sensorNameID): ?Temperature
    {
        $qb = $this->createQueryBuilder(Temperature::READING_TYPE);
        $expr = $qb->expr();

        $qb->select(Temperature::READING_TYPE)
            ->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, Temperature::READING_TYPE.'.sensorNameID = '.Sensor::ALIAS.'.sensorNameID')
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
