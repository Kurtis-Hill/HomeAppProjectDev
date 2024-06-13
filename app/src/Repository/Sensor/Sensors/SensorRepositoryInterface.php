<?php

namespace App\Repository\Sensor\Sensors;

use App\DTOs\Sensor\Internal\Sensor\GetSensorQueryDTO;
use App\DTOs\UserInterface\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @method Sensor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sensor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sensor[]    findAll()
 * @method Sensor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface SensorRepositoryInterface
{
    public function findOneById(int $id): ?Sensor;

    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(Sensor $sensorReadingData): void;

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function flush(): void;

    /**
     * @throws ORMException
     */
    public function remove(Sensor $sensors): void;

    public function findDuplicateSensorOnDeviceByGroup(Sensor $sensorData): ?Sensor;

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findSensorObjectByDeviceIdAndSensorName(int $deviceId, string $sensorName): ?Sensor;

    #[ArrayShape([Sensor::class])]
    public function findSensorObjectsByDeviceID(int $deviceId): array;

    #[ArrayShape([Sensor::class])]
    public function findSensorsByQueryParameters(GetSensorQueryDTO $getSensorQueryDTO): array;

    #[ArrayShape([Sensor::class])]
    public function findSensorsObjectByDeviceIDAndPinNumber(int $deviceID, int $pinNumber): array;

    #[ArrayShape([Sensor::class])]
    public function findAllBusSensors(int $deviceID, int $sensorTypeID, int $pinNumber): array;

    #[ArrayShape([Sensor::class])]
    public function findSameSensorTypesOnSameDevice(int $deviceID, int $sensorType): array;

    #[ArrayShape([Sensor::class])]
    public function findSensorsByIDNoCache(array $sensorIDs, string $orderBy = 'ASC'): array;

    public function findSensorReadingTypeDataBySensor(
        Sensor $sensors,
        array $sensorTypeJoinDTOs
    ): SensorTypeInterface;

    public function findSensorTypeAndReadingTypeObjectsForSensor(
        int $deviceID,
        string $sensorsName,
        JoinQueryDTO $joinQueryDTO = null,
        array $readingTypeJoinQueryDTOs = [],
    ): array;

    /**
     * @throws NonUniqueResultException
     */
    public function findSensorByIDNoCache(int $sensorID): ?Sensor;
}
