<?php

namespace App\Sensors\Repository\ConstRecord\ORM;

use App\Sensors\Entity\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Sensors\Entity\ConstantRecording\ConstLatitude;
use App\Sensors\Repository\ConstRecord\ConstantlyRecordRepositoryInterface;
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
