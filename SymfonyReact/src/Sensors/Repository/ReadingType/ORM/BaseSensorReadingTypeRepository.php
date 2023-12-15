<?php

namespace App\Sensors\Repository\ReadingType\ORM;

use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BaseSensorReadingType>
 *
 * @method BaseSensorReadingType|null find($id, $lockMode = null, $lockVersion = null)
 * @method BaseSensorReadingType|null findOneBy(array $criteria, array $orderBy = null)
 * @method BaseSensorReadingType[]    findAll()
 * @method BaseSensorReadingType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BaseSensorReadingTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BaseSensorReadingType::class);
    }

    /**
     * @throws ORMException
     */
    public function persist(BaseSensorReadingType $baseSensorReadingType): void
    {
        $this->_em->persist($baseSensorReadingType);
    }

    /**
     * @throws ORMException|OptimisticLockException
     */
    public function flush(): void
    {
        $this->_em->flush();
    }
}
