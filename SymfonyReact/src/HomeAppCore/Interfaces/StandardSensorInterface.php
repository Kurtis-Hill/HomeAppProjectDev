<?php

namespace App\HomeAppCore\Interfaces;


use App\Entity\Core\GroupNames;
use App\Entity\Core\Room;
use App\Entity\Sensors\Devices;
use App\Entity\Sensors\Sensors;

interface StandardSensorInterface
{
    public function getSensorID(): ?int;

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
    public function getCurrentSensorReading(): ?float;

    public function getHighReading(): ?float;

    public function getLowReading(): ?float;

    public function getTime(): \DateTime;

    public function setCurrentSensorReading(?float $reading): void;

    public function setHighReading(?float $reading): void;

    public function setLowReading(?float $reading): void;

    public function setTime(\DateTime $dateTime): void;

    /**
     * Sensor Functional Methods
     */
    public function getConstRecord(): ?bool;

    public function setConstRecord(?bool $constrecord);




}
