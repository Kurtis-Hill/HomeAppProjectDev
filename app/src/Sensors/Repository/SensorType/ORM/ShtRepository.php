<?php

namespace App\Sensors\Repository\SensorType\ORM;

use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Sht;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sht>
 *
 * @method Sht|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sht|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sht[]    findAll()
 * @method Sht[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShtRepository extends ServiceEntityRepository implements GenericSensorTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sht::class);
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
