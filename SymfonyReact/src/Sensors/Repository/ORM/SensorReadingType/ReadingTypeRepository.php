<?php

namespace App\Sensors\Repository\ORM\SensorReadingType;

use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<ReadingTypeRepository>
 *
 * @method ReadingTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReadingTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReadingTypes[]    #[ArrayShape([ReadingTypes::class])]
findAll()
 * @method ReadingTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReadingTypeRepository extends ServiceEntityRepository implements ReadingTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReadingTypes::class);
    }
}
