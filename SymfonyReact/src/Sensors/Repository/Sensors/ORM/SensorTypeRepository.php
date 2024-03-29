<?php

namespace App\Sensors\Repository\Sensors\ORM;

use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Repository\Sensors\SensorTypeRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<SensorTypeRepository>
 *
 * @method SensorType|null find($id, $lockMode = null, $lockVersion = null)
 * @method SensorType|null findOneBy(array $criteria, array $orderBy = null)
 * @method SensorType[]    findAll()
 * @method SensorType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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

    #[ArrayShape([Bmp::NAME, Dallas::NAME, Dht::NAME, Soil::NAME, GenericRelay::NAME, GenericMotion::NAME])]
    public function findAllSensorTypeNames(): array
    {
        $qb = $this->createQueryBuilder('st');
        $qb->select('st.sensorType');

        return array_map('current', $qb->getQuery()->getResult());
    }

    #[ArrayShape([Bmp::class, Dallas::class, Dht::class, Soil::class])]
    public function findAllSensorTypes(bool $cache = true): array
    {
        $qb = $this->createQueryBuilder('st');
        if ($cache === true) {
            $qb->setCacheable(true);
        }
        $qb->select();
        $qb->orderBy('st.sensorType', 'ASC');

        return $qb->getQuery()->execute();

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
