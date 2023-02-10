<?php

namespace App\Sensors\Repository\ReadingType\ORM;

use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\ReadingType\ReadingTypeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Humidity>
 *
 * @method Humidity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Humidity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Humidity[]    findAll()
 * @method Humidity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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

    public function findOneById(int $id): ?Humidity
    {
        return $this->find($id);
    }


    public function getOneBySensorNameID(int $sensorNameID): ?Humidity
    {
        $qb = $this->createQueryBuilder(Humidity::READING_TYPE);
        $expr = $qb->expr();

        $qb->select(Humidity::READING_TYPE)
            ->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, Humidity::READING_TYPE.'.sensor = '.Sensor::ALIAS.'.sensor')
            ->where(
                $expr->eq(
                    Sensor::ALIAS.'.sensor',
                    ':sensor'
                )
            )
            ->setParameters(['sensor' => $sensorNameID]);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
