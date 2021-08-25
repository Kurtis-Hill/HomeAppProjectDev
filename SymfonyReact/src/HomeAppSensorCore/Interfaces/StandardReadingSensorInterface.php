<?php

namespace App\HomeAppSensorCore\Interfaces;


use App\Entity\Core\GroupNames;
use App\Entity\Core\Room;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\Sensors;

interface StandardReadingSensorInterface
{
    /**
     * Sensor relational Objects
     */
    public function getSensorObject(): Sensors;

//    public function getDeviceObject(): Devices;

    public function setSensorNameID(Sensors $id);

//    public function setDeviceNameID(Devices $id);

    /**
     * Sensor Reading Methods
     */
    public function getCurrentReading(): int|float;

    public function getHighReading(): int|float;

    public function getLowReading(): int|float;

    public function getTime(): \DateTimeInterface;

    public function setCurrentSensorReading(int|float $reading): void;

    public function setHighReading(int|float|string $reading): void;

    public function setLowReading(int|float|string $reading): void;

    public function setTime(?\DateTime $time = null): void;

    /**
     * Sensor Functional Methods
     */
    public function getMeasurementDifferenceHighReading(): int|float;

    public function getMeasurementDifferenceLowReading(): int|float;
}
