<?php

namespace App\ESPDeviceSensor\Repository\ORM\Sensors;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\Entity\Sensor;
use Doctrine\ORM\ORMException;

interface SensorRepositoryInterface
{
    public function findOneById(int $id): ?Sensor;

    public function persist(Sensor $sensorReadingData): void;

    /**
     * @throws ORMException
     */
    public function flush(): void;

    /**
     * @throws ORMException
     */
    public function remove(Sensor $sensors): void;

    public function checkForDuplicateSensorOnDevice(Sensor $sensorData): ?Sensor;

    public function getSelectedSensorReadingTypeObjectsBySensorNameAndDevice(Devices $device, string $sensors, array $sensorData): array;

    public function checkPersistance();
}
