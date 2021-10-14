<?php

namespace App\ESPDeviceSensor\Repository\ORM\Sensors;


use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\Entity\Sensors;

interface SensorRepositoryInterface
{
    public function persist(Sensors $sensorReadingData): void;

    public function flush(): void;

    public function remove(Sensors $sensors): void;

    public function checkForDuplicateSensorOnDevice(Sensors $sensorData): ?Sensors;

    public function getSensorReadingTypeObjectsBySensorNameAndDevice(Devices $device, string $sensors, array $sensorData): array;
}
