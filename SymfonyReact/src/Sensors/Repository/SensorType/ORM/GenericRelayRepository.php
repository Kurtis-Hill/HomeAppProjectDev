<?php

namespace App\Sensors\Repository\SensorType\ORM;

use App\Sensors\Entity\SensorTypes\GenericRelay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GenericRelay>
 *
 * @method GenericRelay|null find($id, $lockMode = null, $lockVersion = null)
 * @method GenericRelay|null findOneBy(array $criteria, array $orderBy = null)
 * @method GenericRelay[]    findAll()
 * @method GenericRelay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GenericRelayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GenericRelay::class);
    }
}
