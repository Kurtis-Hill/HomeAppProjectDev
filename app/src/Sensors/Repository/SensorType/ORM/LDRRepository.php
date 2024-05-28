<?php

namespace App\Sensors\Repository\SensorType\ORM;

use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\LDR;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LDR>
 *
 * @method LDR|null find($id, $lockMode = null, $lockVersion = null)
 * @method LDR|null findOneBy(array $criteria, array $orderBy = null)
 * @method LDR[]    findAll()
 * @method LDR[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LDRRepository extends ServiceEntityRepository implements GenericSensorTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LDR::class);
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
