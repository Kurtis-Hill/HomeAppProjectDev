<?php

namespace App\Repository\Sensor\ConstRecord\ORM;

use App\Entity\Sensor\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Entity\Sensor\ConstantRecording\ConstHumid;
use App\Repository\Sensor\ConstRecord\ConstantlyRecordRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConstHumid>
 *
 * @method ConstHumid|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConstHumid|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConstHumid[]    findAll()
 * @method ConstHumid[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstantlyRecordHumidRepository extends ServiceEntityRepository implements ConstantlyRecordRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstHumid::class);
    }

    public function persist(ConstantlyRecordEntityInterface $sensorReadingData): void
    {
        $this->getEntityManager()->persist($sensorReadingData);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
