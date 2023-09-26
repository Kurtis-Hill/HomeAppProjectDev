<?php

namespace App\Sensors\Repository;

use App\Sensors\Entity\SensorTrigger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SensorTrigger>
 *
 * @method SensorTrigger|null find($id, $lockMode = null, $lockVersion = null)
 * @method SensorTrigger|null findOneBy(array $criteria, array $orderBy = null)
 * @method SensorTrigger[]    findAll()
 * @method SensorTrigger[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SensorTriggerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SensorTrigger::class);
    }

    public function persist(SensorTrigger $sensorTrigger): void
    {
        $this->_em->persist($sensorTrigger);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }
}
