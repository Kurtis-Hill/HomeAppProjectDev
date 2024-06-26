<?php

namespace App\Repository\Sensor\SensorReadingType\ORM;

use App\Entity\Sensor\ReadingTypes\ReadingTypes;
use App\Repository\Sensor\SensorReadingType\ReadingTypeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<ReadingTypeRepository>
 *
 * @method ReadingTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReadingTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReadingTypes[]    findAll()
 * @method ReadingTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReadingTypeRepository extends ServiceEntityRepository implements ReadingTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReadingTypes::class);
    }

    #[ArrayShape([ReadingTypes::class])]
    public function findAllPaginatedResults(int $limit, int $offset): array
    {
        return $this->createQueryBuilder('readingTypes')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
