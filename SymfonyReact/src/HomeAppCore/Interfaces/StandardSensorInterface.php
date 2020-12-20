<?php

namespace App\HomeAppCore\Interfaces;


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
    public function getGroupNameID(): GroupNames;

    public function getRoomID(): Room;

    public function getSensorNameID(): Sensors;

    public function getDeviceNameID(): Devices;

    public function setGroupNameID(GroupNames $id);

    public function setRoomID(Room $id);

    public function setSensorNameID(Sensors $id);

    /**
     * Sensor Reading Methods
     */
    public function getCurrentSensorReading(): int|float;

    public function getHighReading(): int|float;

    public function getLowReading(): int|float;

    public function getTime(): \DateTime;

    public function setCurrentSensorReading(int|float $reading): void;

    public function setHighReading(int|float $reading): void;

    public function setLowReading(int|float $reading): void;

    public function setTime(\DateTime $dateTime): void;

    /**
     * Sensor Functional Methods
     */
    public function getConstRecord(): ?bool;

    public function setConstRecord(?bool $constrecord);




}
