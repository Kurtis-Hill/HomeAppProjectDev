<?php

namespace App\Sensors\Repository\SensorType\ORM;

use App\Sensors\Entity\SensorTypes\GenericMotion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GenericMotion>
 *
 * @method GenericMotion|null find($id, $lockMode = null, $lockVersion = null)
 * @method GenericMotion|null findOneBy(array $criteria, array $orderBy = null)
 * @method GenericMotion[]    findAll()
 * @method GenericMotion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenericMotionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GenericMotion::class);
    }
}
