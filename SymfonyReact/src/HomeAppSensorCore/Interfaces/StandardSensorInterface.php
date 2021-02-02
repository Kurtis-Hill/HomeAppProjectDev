<?php

namespace App\HomeAppSensorCore\Interfaces;


use App\Entity\Core\GroupNames;
use App\Entity\Core\Room;
use App\Entity\Sensors\Devices;
use App\Entity\Sensors\Sensors;

interface StandardSensorInterface
{
    public function getSensorID(): int;

    public function setSensorID(int $id);

    /**
     * Sensor relational Objects
     */
    public function getSensorObject(): Sensors;

    public function getDeviceObject(): Devices;

    public function setSensorNameID(Sensors $id);

    public function setDeviceNameID(Devices $id);

    /**
     * Sensor Reading Methods
     */
    public function getCurrentSensorReading(): int|float;

    public function getHighReading(): int|float;

    public function getLowReading(): int|float;

    public function getTime(): \DateTimeInterface;

    public function setCurrentSensorReading(int|float $reading): void;

    public function setHighReading(int|float $reading): void;

    public function setLowReading(int|float $reading): void;

    public function setTime(\DateTimeInterface $dateTime): void;

    /**
     * Sensor Functional Methods
     */
    public function getConstRecord(): bool;

    public function setConstRecord(bool $constrecord);

    public function getMeasurementDifferenceHighReading(): int|float;

    public function getMeasurementDifferenceLowReading(): int|float;


}
