<?php

namespace App\Sensors\Repository\ReadingType\ORM;

use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Repository\ReadingType\ReadingTypeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<Latitude>
 *
 * @method Latitude|null find($id, $lockMode = null, $lockVersion = null)
 * @method Latitude|null findOneBy(array $criteria, array $orderBy = null)
 * @method Latitude[]    findAll()
 * @method Latitude[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LatitudeRepository extends ServiceEntityRepository implements ReadingTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Latitude::class);
    }

    public function persist(AllSensorReadingTypeInterface $readingTypeObject): void
    {
        $this->getEntityManager()->persist($readingTypeObject);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findOneById(int $id): ?Latitude
    {
        return $this->find($id);
    }

    public function removeObject(AllSensorReadingTypeInterface $readingTypeObject)
    {
        $this->getEntityManager()->remove($readingTypeObject);
    }

    public function findOneBySensorNameID(int $sensorNameID): ?Latitude
    {
        $qb = $this->createQueryBuilder(Latitude::READING_TYPE);
        $expr = $qb->expr();

        $qb->select(Latitude::READING_TYPE)
            ->innerJoin(BaseSensorReadingType::class, BaseSensorReadingType::ALIAS, Join::WITH, Latitude::READING_TYPE.'.baseReadingType = '.BaseSensorReadingType::ALIAS.'.baseReadingTypeID')
            ->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, BaseSensorReadingType::ALIAS.'.sensor = '.Sensor::ALIAS.'.sensorID')
            ->where(
                $expr->eq(
                    Sensor::ALIAS.'.sensorID',
                    ':sensor'
                )
            )
            ->setParameters(['sensor' => $sensorNameID]);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findOneBySensorName(string $sensorName): ?Latitude
    {
        $qb = $this->createQueryBuilder(Latitude::READING_TYPE);
        $expr = $qb->expr();

        $qb->select(Latitude::READING_TYPE)
            ->innerJoin(BaseSensorReadingType::class, BaseSensorReadingType::ALIAS, Join::WITH, Latitude::READING_TYPE.'.baseReadingType = '.BaseSensorReadingType::ALIAS.'.baseReadingTypeID')
            ->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, BaseSensorReadingType::ALIAS.'.sensor = '.Sensor::ALIAS.'.sensorID')
            ->where(
                $expr->eq(
                    Sensor::ALIAS.'.sensorName',
                    ':sensor'
                )
            )
            ->setParameters(['sensor' => $sensorName]);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function refresh(AllSensorReadingTypeInterface $readingTypeObject): void
    {
        $this->getEntityManager()->refresh($readingTypeObject);
    }

    /**
     * @return Latitude[]
     */
    #[ArrayShape([Latitude::class])]
    public function findBySensorID(int $sensorID): array
    {
        $qb = $this->createQueryBuilder('readingType');
        $expr = $qb->expr();

        $qb->select('readingType')
            ->innerJoin(BaseSensorReadingType::class, 'baseReadingType', Join::WITH, 'readingType.baseReadingType = baseReadingType.baseReadingTypeID')
            ->where(
                $expr->eq(
                    'baseReadingType.sensor',
                    ':sensor'
                )
            )
            ->setParameters(['sensor' => $sensorID]);

        return $qb->getQuery()->getResult();
    }
}
