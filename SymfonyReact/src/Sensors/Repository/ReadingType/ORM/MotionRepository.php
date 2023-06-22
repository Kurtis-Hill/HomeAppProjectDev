<?php

namespace App\Sensors\Repository\ReadingType\ORM;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MotionRepository>
 *
 * @method Motion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Motion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Motion[]    findAll()
 * @method Motion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MotionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Motion::class);
    }
}
