<?php

namespace App\Sensors\Repository\Sensors;

use App\Devices\Entity\Devices;
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

    public function checkForDuplicateSensorOnDevice(Sensor $sensorData): ?Sensor;

    public function getSelectedSensorReadingTypeObjectsBySensorNameAndDevice(Devices $device, string $sensors, array $sensorData): array;

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findSensorObjectByDeviceIdAndSensorName(int $deviceId, string $sensorName): ?Sensor;

    #[ArrayShape([Sensor::class])]
    public function findSensorObjectsByDeviceID(int $deviceId): array;

}
