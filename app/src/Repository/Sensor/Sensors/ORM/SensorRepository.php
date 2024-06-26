<?php

namespace App\Repository\Sensor\Sensors\ORM;

use App\DTOs\Sensor\Internal\Sensor\GetSensorQueryDTO;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\Entity\Device\Devices;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Entity\UserInterface\Card\CardView;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Traits\QueryJoinBuilderTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<SensorRepository>
 *
 * @method Sensor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sensor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sensor[]    findAll()
 * @method Sensor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SensorRepository extends ServiceEntityRepository implements SensorRepositoryInterface
{
    use QueryJoinBuilderTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sensor::class);
    }

    public function persist(Sensor $sensorReadingData): void
    {
        $this->getEntityManager()->persist($sensorReadingData);
    }

    public function findOneById(int $id): ?Sensor
    {
        return $this->find($id);
    }

    public function flush(): void
    {
        //refresh the cache
//        $this->getEntityManager()->getConfiguration()->getResultCache()?->clear();
        $this->getEntityManager()->flush();
//        $this->getEntityManager()->refresh();
    }

    public function remove(Sensor $sensors): void
    {
        $this->getEntityManager()->remove($sensors);
    }

    public function findDuplicateSensorOnDeviceByGroup(Sensor $sensorData): ?Sensor
    {
        $qb = $this->createQueryBuilder('sensor');
        $expr = $qb->expr();

        $qb->select('sensor')
            ->innerJoin(Devices::class, 'device', Join::WITH, Devices::ALIAS.'.deviceID = sensor.deviceID')
            ->where(
                $expr->eq('sensor.sensorName', ':sensorName'),
                $expr->eq('device.groupID', ':groupName'),
            )
            ->setParameters(
                [
                    'sensorName' => $sensorData->getSensorName(),
                    'groupName' => $sensorData->getDevice()->getGroupObject(),
                ]
            );

        return $qb->getQuery()->getOneOrNullResult();
    }

    #[ArrayShape([Sensor::class])]
    public function findSensorsObjectByDeviceIDAndPinNumber(int $deviceID, int $pinNumber): array
    {
        $qb = $this->createQueryBuilder('sensor');

        $qb->select('sensor')
            ->innerJoin(Devices::class, 'device', Join::WITH, Devices::ALIAS.'.deviceID = sensor.deviceID')
            ->where(
                $qb->expr()->eq('sensor.pinNumber', ':pinNumber'),
                $qb->expr()->eq('device.deviceID', ':deviceID')
            )
            ->setParameters(
                [
                    'pinNumber' => $pinNumber,
                    'deviceID' => $deviceID,
                ]
            );

        return $qb->getQuery()->getResult();
    }

    public function findSensorReadingTypeDataBySensor(
        Sensor $sensors,
        array $sensorTypeJoinDTOs
    ): SensorTypeInterface {
        $qb = $this->createQueryBuilder(Sensor::ALIAS);

        $sensorAlias = $this->prepareSensorJoinsForQuery($sensorTypeJoinDTOs, $qb);

        $qb->select($sensorAlias)
            ->where(
                $qb->expr()->eq(Sensor::ALIAS. '.sensorID', ':id')
            )
            ->setParameters(['id' => $sensors]);

        $result = array_filter($qb->getQuery()->getResult());
        $result = array_values($result);

        return $result[0];
    }

    #[ArrayShape([
        Sensor::class | Dht::class | Bmp::class | Dallas::class | Soil::class,
        Temperature::class, Analog::class, Humidity::class, Latitude::class,
    ])]
    public function findSensorTypeAndReadingTypeObjectsForSensor(
        int $deviceID,
        string $sensorsName,
        JoinQueryDTO $joinQueryDTO = null,
        array $readingTypeJoinQueryDTOs = [],
    ): array {
        $qb = $this->createQueryBuilder('sensors');
        $qb->leftJoin(
            BaseSensorReadingType::class,
            BaseSensorReadingType::ALIAS,
            Join::WITH,
            'sensors.sensorID = ' . BaseSensorReadingType::ALIAS . '.sensor'
        );
        if (!empty($readingTypeJoinQueryDTOs)) {
            $readingTypes = $this->prepareSensorJoinsForQuery($readingTypeJoinQueryDTOs, $qb);
            $selects[] = $readingTypes;
        }
        if ($joinQueryDTO !== null) {
            $sensorTypes = $this->prepareSensorJoinsForQuery([$joinQueryDTO], $qb);
            $selects[] = $sensorTypes;
        }

        $qb->select($selects ?? ['']);
        $qb->innerJoin(
            Devices::class,
            'device',
            Join::WITH,
            'sensors.deviceID = device.deviceID'
        )
            ->where(
                $qb->expr()->eq('sensors.sensorName', ':sensorName'),
                $qb->expr()->eq('sensors.deviceID', ':deviceID')
            )
            ->setParameters([
                'sensorName' => $sensorsName,
                'deviceID' => $deviceID
            ]);

        return array_filter($qb->getQuery()->getResult());
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findSensorObjectByDeviceIdAndSensorName(int $deviceId, string $sensorName): ?Sensor
    {
        $qb = $this->createQueryBuilder('sensor');

        $qb->select('sensor')
            ->innerJoin(Devices::class, 'device', Join::WITH, 'device.deviceID = sensor.deviceID')
            ->where(
                $qb->expr()->eq('sensor.sensorName', ':sensorName'),
                $qb->expr()->eq('device.deviceID', ':deviceID')
            )
            ->setParameters(
                [
                    'sensorName' => $sensorName,
                    'deviceID' => $deviceId,
                ]
            );

        return $qb->getQuery()->getOneOrNullResult();
    }

    #[ArrayShape([Sensor::class])]
    public function findSensorObjectsByDeviceID(int $deviceId): array
    {
        $qb = $this->createQueryBuilder(Sensor::ALIAS);

        $qb->select(Sensor::ALIAS)
            ->innerJoin(Devices::class, Devices::ALIAS, Join::WITH, Devices::ALIAS . '.deviceID = ' . Sensor::ALIAS . '.deviceID')
            ->where(
                $qb->expr()->eq(Devices::ALIAS . '.deviceID', ':deviceID')
            )
            ->setParameters(
                [
                    'deviceID' => $deviceId,
                ]
            );

        return $qb->getQuery()->getResult();
    }

    #[ArrayShape([Sensor::class])]
    public function findSensorsByQueryParameters(GetSensorQueryDTO $getSensorQueryDTO): array
    {
        $qb = $this->createQueryBuilder(Sensor::ALIAS);

        $qb->select(Sensor::ALIAS);

        if ($getSensorQueryDTO->getDeviceIDs() !== null) {
            $qb->innerJoin(Devices::class, Devices::ALIAS, Join::WITH, Devices::ALIAS . '.deviceID = ' . Sensor::ALIAS . '.deviceID')
                ->andWhere(
                    $qb->expr()->in(Devices::ALIAS . '.deviceID', ':deviceID'),

                )
                ->setParameters(
                    [
                        'deviceID' => $getSensorQueryDTO->getDeviceIDs(),
                    ]
                );
        }

        if ($getSensorQueryDTO->getDeviceNames() !== null) {
            $qb->innerJoin(Devices::class, Devices::ALIAS . '2', Join::WITH, Devices::ALIAS . '2.deviceID = ' . Sensor::ALIAS . '.deviceID')
                ->andWhere(
                    $qb->expr()->in(Devices::ALIAS . '2' . '.deviceName', ':deviceNames')
                )
                ->setParameters(
                    [
                        'deviceNames' => $getSensorQueryDTO->getDeviceNames(),
                    ]
                );
        }

        if ($getSensorQueryDTO->getGroupIDs() !== null) {
            $qb->innerJoin(Devices::class, Devices::ALIAS . '3', Join::WITH, Devices::ALIAS . '3.deviceID = ' . Sensor::ALIAS . '.deviceID')
                ->andWhere(
                    $qb->expr()->in(Devices::ALIAS . '3' . '.groupID', ':groupIDs')
                )
                ->setParameters(
                    [
                        'groupIDs' => $getSensorQueryDTO->getGroupIDs(),
                    ]
                );
        }

        if ($getSensorQueryDTO->getCardViewIDs() !== null) {
            $qb->innerJoin(CardView::class, CardView::ALIAS, Join::WITH, CardView::ALIAS . '.sensor = ' . Sensor::ALIAS . '.sensorID')
                ->andWhere(
                    $qb->expr()->in(CardView::ALIAS . '.cardViewID', ':cardViewIDs')
                )
                ->setParameters(
                    [
                        'cardViewIDs' => $getSensorQueryDTO->getCardViewIDs(),
                    ]
                );
        }

        $qb->setFirstResult($getSensorQueryDTO->getOffset())
            ->setMaxResults($getSensorQueryDTO->getLimit());
        return $qb->getQuery()->getResult();
    }

    #[ArrayShape([Sensor::class])]
    public function findAllBusSensors(int $deviceID, int $sensorTypeID, int $pinNumber): array
    {
        $qb = $this->createQueryBuilder(Sensor::ALIAS);

        $qb->select(Sensor::ALIAS)
            ->innerJoin(Devices::class, Devices::ALIAS, Join::WITH, Devices::ALIAS . '.deviceID = ' . Sensor::ALIAS . '.deviceID')
            ->where(
                $qb->expr()->eq(Devices::ALIAS . '.deviceID', ':deviceID'),
                $qb->expr()->eq(Sensor::ALIAS . '.sensorTypeID', ':sensorTypeID'),
                $qb->expr()->eq(Sensor::ALIAS . '.pinNumber', ':pinNumber')
            )
            ->setParameters(
                [
                    'deviceID' => $deviceID,
                    'sensorTypeID' => $sensorTypeID,
                    'pinNumber' => $pinNumber,
                ]
            );

        return $qb->getQuery()->getResult();
    }

    #[ArrayShape([Sensor::class])]
    public function findSameSensorTypesOnSameDevice(int $deviceID, int $sensorType): array
    {
        $qb = $this->createQueryBuilder(Sensor::ALIAS);
        $expr = $qb->expr();

        $qb->select()
            ->where(
                $expr->eq(Sensor::ALIAS . '.deviceID', ':deviceID'),
                $expr->eq(Sensor::ALIAS . '.sensorTypeID', ':sensorTypeID')
            )
            ->orderBy(Sensor::ALIAS . '.createdAt', 'ASC')
            ->setParameters(
                [
                    'deviceID' => $deviceID,
                    'sensorTypeID' => $sensorType,
                ]
            )
            ->orderBy(Sensor::ALIAS . '.createdAt', 'ASC');

        return $qb->getQuery()->getResult();
    }

    #[ArrayShape([Sensor::class])]
    public function findSensorsByIDNoCache(array $sensorIDs, string $orderBy = 'ASC'): array
    {
        $qb = $this->createQueryBuilder(Sensor::ALIAS);
        $expr = $qb->expr();

        $qb->setCacheable(false);
        $qb->select()
            ->where(
                $expr->in(Sensor::ALIAS . '.sensorID', ':sensorIDs')
            )
            ->orderBy(Sensor::ALIAS . '.createdAt', $orderBy)
            ->setParameters(
                [
                    'sensorIDs' => $sensorIDs,
                ]
            );

        return $qb->getQuery()->execute();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findSensorByIDNoCache(int $sensorID): ?Sensor
    {
        $qb = $this->createQueryBuilder(Sensor::ALIAS);
        $expr = $qb->expr();

        $qb->setCacheable(false);
        $qb->select()
            ->where(
                $expr->eq(Sensor::ALIAS . '.sensorID', ':sensorID')
            )
            ->setParameters(
                [
                    'sensorID' => $sensorID,
                ]
            );

        return $qb->getQuery()->getOneOrNullResult();
    }
}
