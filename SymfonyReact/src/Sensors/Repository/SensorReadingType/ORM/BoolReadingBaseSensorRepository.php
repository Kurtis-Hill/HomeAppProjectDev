<?php

namespace App\Sensors\Repository\SensorReadingType\ORM;

use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\AbstractBoolReadingBaseSensor;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReadingTypeRepository>
 *
 * @method AbstractBoolReadingBaseSensor|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractBoolReadingBaseSensor|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractBoolReadingBaseSensor[]    findAll()
 * @method AbstractBoolReadingBaseSensor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoolReadingBaseSensorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractBoolReadingBaseSensor::class);
    }

    /**
     * @return BoolReadingSensorInterface[]
     */
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
