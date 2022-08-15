<?php

namespace App\Sensors\Repository\ORM\ReadingType;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

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

    public function getOneBySensorNameID(int $sensorNameID): ?Latitude
    {
        $qb = $this->createQueryBuilder(Latitude::READING_TYPE);
        $expr = $qb->expr();

        $qb->select(Latitude::READING_TYPE)
            ->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, Latitude::getReadingTypeName().'.sensorNameID = '.Sensor::ALIAS.'.sensorNameID')
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
