<?php

namespace App\Sensors\Entity\SensorTypes\Interfaces;

use App\Sensors\Entity\Sensor;
use App\UserInterface\Entity\Card\CardView;

interface SensorTypeInterface
{
    public function setSensorObject(Sensor $id);

    public function getSensorTypeID(): int;

    public function getSensorObject(): Sensor;

    // make sure this returns the same data as in the seensortype table in the sensorType column
    public function getSensorTypeName(): string;

    public function getCardViewObject(): ?CardView;

    public function getSensorClass(): string;

    public function getSensorTypeAlias(): string;

}
