<?php

namespace App\Repository\Sensor\ConstRecord\ORM;

use App\Entity\Sensor\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Entity\Sensor\ConstantRecording\ConstLatitude;
use App\Repository\Sensor\ConstRecord\ConstantlyRecordRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConstLatitude>
 *
 * @method ConstLatitude|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConstLatitude|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConstLatitude[]    findAll()
 * @method ConstLatitude[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConstantlyRecordLatitudeRepository extends ServiceEntityRepository implements ConstantlyRecordRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConstLatitude::class);
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
