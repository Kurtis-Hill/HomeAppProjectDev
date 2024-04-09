<?php

namespace App\Sensors\Repository\ReadingType\ORM;

use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Repository\ReadingType\ReadingTypeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<MotionRepository>
 *
 * @method Motion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Motion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Motion[]    findAll()
 * @method Motion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotionRepository extends ServiceEntityRepository implements ReadingTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Motion::class);
    }

    public function persist(AllSensorReadingTypeInterface $readingTypeObject): void
    {
        $this->getEntityManager()->persist($readingTypeObject);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findOneById(int $id): ?Motion
    {
        return $this->find($id);
    }


    public function findOneBySensorNameID(int $sensorNameID): ?Motion
    {
        $qb = $this->createQueryBuilder(Motion::READING_TYPE);
        $expr = $qb->expr();

        $qb->select(Motion::READING_TYPE)
            ->innerJoin(BaseSensorReadingType::class, BaseSensorReadingType::ALIAS, Join::WITH, Motion::READING_TYPE.'.baseReadingType = '.BaseSensorReadingType::ALIAS.'.baseReadingTypeID')
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

    public function findOneBySensorName(string $sensorName): ?Motion
    {
        $qb = $this->createQueryBuilder(Motion::READING_TYPE);
        $expr = $qb->expr();

        $qb->select(Motion::READING_TYPE)
            ->innerJoin(BaseSensorReadingType::class, BaseSensorReadingType::ALIAS, Join::WITH, Motion::READING_TYPE.'.baseReadingType = '.BaseSensorReadingType::ALIAS.'.baseReadingTypeID')
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
     * @return Motion[]
     */
    #[ArrayShape([Motion::class])]
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
