<?php

namespace App\Sensors\Repository\ReadingType\ORM;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\ReadingType\ReadingTypeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Temperature>
 *
 * @method Temperature|null find($id, $lockMode = null, $lockVersion = null)
 * @method Temperature|null findOneBy(array $criteria, array $orderBy = null)
 * @method Temperature[]    findAll()
 * @method Temperature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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

    public function findOneById(int $id): ?Temperature
    {
        return $this->find($id);
    }

    public function findAllBySensorName(string $name): array
    {
        $qb = $this->createQueryBuilder(Temperature::getReadingTypeName());
        $expr = $qb->expr();

        $qb->select(Temperature::getReadingTypeName())
            ->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, Temperature::getReadingTypeName().'.sensorNameID = '.Sensor::ALIAS.'.sensorNameID')
            ->where(
                $expr->eq(
                    Sensor::ALIAS.'.sensorName',
                    ':sensorName'
                )
            )
            ->setParameters(['sensorName' => $name]);

        return $qb->getQuery()->getResult() ?? [];
    }

    public function getOneBySensorNameID(int $sensorNameID): ?Temperature
    {
        $qb = $this->createQueryBuilder(Temperature::getReadingTypeName());
        $expr = $qb->expr();

        $qb->select(Temperature::getReadingTypeName())
            ->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, Temperature::getReadingTypeName().'.sensorNameID = '.Sensor::ALIAS.'.sensorNameID')
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
