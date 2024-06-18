<?php

namespace App\Repository\Sensor\SensorType\ORM;

use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<\App\Entity\Sensor\SensorTypes\Dallas>
 *
 * @method Dallas|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dallas|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dallas[]    findAll()
 * @method Dallas[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DallasRepository extends ServiceEntityRepository implements GenericSensorTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dallas::class);
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
