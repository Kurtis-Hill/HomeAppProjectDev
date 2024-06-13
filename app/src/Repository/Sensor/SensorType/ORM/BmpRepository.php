<?php

namespace App\Repository\Sensor\SensorType\ORM;

use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BmpRepository>
 *
 * @method BmpRepository|null find($id, $lockMode = null, $lockVersion = null)
 * @method BmpRepository|null findOneBy(array $criteria, array $orderBy = null)
 * @method BmpRepository[]    findAll()
 * @method BmpRepository[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BmpRepository extends ServiceEntityRepository implements GenericSensorTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bmp::class);
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
