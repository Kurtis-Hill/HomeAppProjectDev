<?php

namespace App\Sensors\Repository\SensorType\ORM;

use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
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
class GenericRelayRepository extends ServiceEntityRepository implements GenericSensorTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GenericRelay::class);
    }

    public function persist(SensorTypeInterface $sensor): void
    {
        $this->getEntityManager()->persist($sensor);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
