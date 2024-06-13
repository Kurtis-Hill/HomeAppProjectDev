<?php

namespace App\Repository\Sensor\SensorType\ORM;

use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use App\Entity\Sensor\SensorTypes\Soil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<\App\Entity\Sensor\SensorTypes\Soil>
 *
 * @method Soil|null find($id, $lockMode = null, $lockVersion = null)
 * @method Soil|null findOneBy(array $criteria, array $orderBy = null)
 * @method Soil[]    findAll()
 * @method Soil[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SoilRepository extends ServiceEntityRepository implements GenericSensorTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Soil::class);
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
