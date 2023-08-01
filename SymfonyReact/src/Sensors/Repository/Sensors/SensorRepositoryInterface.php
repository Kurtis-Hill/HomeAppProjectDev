<?php

namespace App\Sensors\Repository\Sensors;

use App\Devices\Entity\Devices;
use App\Sensors\DTO\Internal\Sensor\GetSensorQueryDTO;
use App\Sensors\Entity\Sensor;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

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

    public function findSensorObjectByDeviceIDAndPinNumber(int $deviceID, int $pinNumber): ?Sensor;
}
