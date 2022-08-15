<?php

namespace App\Sensors\Repository\ORM\Sensors;

use App\Sensors\Entity\SensorType;
use App\Sensors\Exceptions\SensorTypeException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<SensorTypeRepository>
 *
 * @method SensorRepository|null find($id, $lockMode = null, $lockVersion = null)
 * @method SensorRepository|null findOneBy(array $criteria, array $orderBy = null)
 * @method SensorRepository[]    findAll()
 * @method SensorRepository[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SensorTypeRepository extends ServiceEntityRepository implements SensorTypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SensorType::class);
    }

    public function findOneById(int $id): ?SensorType
    {
        return $this->find($id);
    }

    #[ArrayShape(['Bmp', 'Dallas', 'Dht', 'Soil'])]
    public function getAllSensorTypeNames(): array
    {
        $qb = $this->createQueryBuilder('st');
        $qb->select('st.sensorType');

        return array_map('current', $qb->getQuery()->getResult());
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
