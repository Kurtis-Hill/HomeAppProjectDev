<?php

namespace App\ESPDeviceSensor\Repository\ORM\ReadingType;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

class AnalogRepository extends ServiceEntityRepository implements ReadingTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Analog::class);
    }

    public function persist(AllSensorReadingTypeInterface $readingTypeObject): void
    {
        $this->getEntityManager()->persist($readingTypeObject);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findOneById(int $id)
    {
        return $this->findOneBy(['analogID' => $id]);
    }

    public function removeObject(AllSensorReadingTypeInterface $readingTypeObject)
    {
        $this->getEntityManager()->remove($readingTypeObject);
    }
}
