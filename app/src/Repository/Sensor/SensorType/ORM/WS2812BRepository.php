<?php

namespace App\Repository\Sensor\SensorType\ORM;

use App\Entity\Sensor\ReadingTypes\LEDReadingTypes\WS2812B;
use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WS2812B>
 *
 * @method WS2812B|null find($id, $lockMode = null, $lockVersion = null)
 * @method WS2812B|null findOneBy(array $criteria, array $orderBy = null)
 * @method WS2812B[]    findAll()
 * @method WS2812B[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WS2812BRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WS2812B::class);
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
