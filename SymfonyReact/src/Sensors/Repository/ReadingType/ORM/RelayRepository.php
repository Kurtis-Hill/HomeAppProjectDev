<?php

namespace App\Sensors\Repository\ReadingType\ORM;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MotionRepository>
 *
 * @method Relay|null find($id, $lockMode = null, $lockVersion = null)
 * @method Relay|null findOneBy(array $criteria, array $orderBy = null)
 * @method Relay[]    findAll()
 * @method Relay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RelayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Relay::class);
    }
}
