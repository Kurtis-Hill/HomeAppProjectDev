<?php

namespace App\Sensors\Repository\SensorType\ORM;

use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
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
class GenericMotionRepository extends ServiceEntityRepository implements GenericSensorTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GenericMotion::class);
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
