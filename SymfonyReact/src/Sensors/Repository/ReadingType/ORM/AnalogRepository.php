<?php

namespace App\Sensors\Repository\ReadingType\ORM;

use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\ReadingType\ReadingTypeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AnalogRepository>
 *
 * @method Analog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Analog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Analog[]    findAll()
 * @method Analog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnalogRepository extends ServiceEntityRepository implements ReadingTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Analog::class);
    }

    public function persist(AllSensorReadingTypeInterface $readingTypeObject): void
    {
        $this->getEntityManager()->persist($readingTypeObject);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findOneById(int $id): ?Analog
    {
        return $this->find($id);
    }

    public function removeObject(AllSensorReadingTypeInterface $readingTypeObject)
    {
        $this->getEntityManager()->remove($readingTypeObject);
    }

    public function getOneBySensorNameID(int $sensorNameID): ?Analog
    {
        $qb = $this->createQueryBuilder(Analog::READING_TYPE);
        $expr = $qb->expr();

        $qb->select(Analog::READING_TYPE)
            ->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, Analog::READING_TYPE.'.sensor = '.Sensor::ALIAS.'.sensorID')
            ->where(
                $expr->eq(
                    Sensor::ALIAS.'.sensorID',
                    ':sensor'
                )
            )
            ->setParameters(['sensor' => $sensorNameID]);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
