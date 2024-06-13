<?php

namespace App\Repository\Sensor\ReadingType\ORM;

use App\Entity\Device\Devices;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Repository\Sensor\ReadingType\ReadingTypeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<MotionRepository>
 *
 * @method Relay|null find($id, $lockMode = null, $lockVersion = null)
 * @method Relay|null findOneBy(array $criteria, array $orderBy = null)
 * @method Relay[]    findAll()
 * @method Relay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelayRepository extends ServiceEntityRepository implements ReadingTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Relay::class);
    }

    public function persist(AllSensorReadingTypeInterface $readingTypeObject): void
    {
        $this->getEntityManager()->persist($readingTypeObject);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findOneById(int $id): ?Relay
    {
        return $this->find($id);
    }


    public function findOneBySensorNameID(int $sensorNameID): ?Relay
    {
        $qb = $this->createQueryBuilder(Relay::READING_TYPE);
        $expr = $qb->expr();

        $qb->select(Relay::READING_TYPE)
            ->innerJoin(BaseSensorReadingType::class, BaseSensorReadingType::ALIAS, Join::WITH, Relay::READING_TYPE.'.baseReadingType = '.BaseSensorReadingType::ALIAS.'.baseReadingTypeID')
            ->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, BaseSensorReadingType::ALIAS.'.sensor = '.Sensor::ALIAS.'.sensorID')            ->where(
                $expr->eq(
                    Sensor::ALIAS.'.sensorID',
                    ':sensor'
                )
            )
            ->setParameters(['sensor' => $sensorNameID]);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findOneBySensorName(string $sensorName): ?Relay
    {
        $qb = $this->createQueryBuilder(Relay::READING_TYPE);
        $expr = $qb->expr();

        $qb->select(Relay::READING_TYPE)
            ->innerJoin(BaseSensorReadingType::class, BaseSensorReadingType::ALIAS, Join::WITH, Relay::READING_TYPE.'.baseReadingType = '.BaseSensorReadingType::ALIAS.'.baseReadingTypeID')
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
     * @return \App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay[]
     */
    #[ArrayShape([Relay::class])]
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

    public function findReadingTypeUserHasAccessTo(array $groupsIDs): array
    {
        $qb = $this->createQueryBuilder('readingType');
        $expr = $qb->expr();

        $qb->select('readingType')
            ->innerJoin(BaseSensorReadingType::class, 'baseReadingType', Join::WITH, 'readingType.baseReadingType = baseReadingType.baseReadingTypeID')
            ->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, 'baseReadingType.sensor = ' . Sensor::ALIAS . '.sensorID')
            ->innerJoin(Devices::class, Devices::ALIAS, Join::WITH, Devices::ALIAS . '.deviceID = '. Sensor::ALIAS . '.deviceID')
            ->where(
                $expr->in(
                    Devices::ALIAS . '.groupID',
                    ':groups')
            )
            ->setParameters(['groups' => $groupsIDs]);

        return $qb->getQuery()->getResult();
    }
}
