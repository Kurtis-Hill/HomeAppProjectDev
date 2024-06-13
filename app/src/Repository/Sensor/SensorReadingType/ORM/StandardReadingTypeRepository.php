<?php

namespace App\Repository\Sensor\SensorReadingType\ORM;

use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\AbstractStandardReadingType;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<ReadingTypeRepository>
 *
 * @method AbstractStandardReadingType|null find($id, $lockMode = null, $lockVersion = null)
 * @method AbstractStandardReadingType|null findOneBy(array $criteria, array $orderBy = null)
 * @method AbstractStandardReadingType[]    findAll()
 * @method AbstractStandardReadingType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StandardReadingTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbstractStandardReadingType::class);
    }

    /**
     * @return StandardReadingSensorInterface[]
     */
    #[ArrayShape([StandardReadingSensorInterface::class])]
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
