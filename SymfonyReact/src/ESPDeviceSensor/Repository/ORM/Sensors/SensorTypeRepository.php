<?php

namespace App\ESPDeviceSensor\Repository\ORM\Sensors;

use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SensorTypeRepository extends ServiceEntityRepository implements SensorTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SensorType::class);
    }

    public function findOneById(int $id): ?SensorType
    {
        return $this->findOneBy(['sensorTypeID' => $id]);
    }

    public function persist(SensorType $sensorType): void
    {
        $this->_em->persist($sensorType);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function remove(SensorType $sensorType): void
    {
        $this->_em->remove($sensorType);
    }

}
