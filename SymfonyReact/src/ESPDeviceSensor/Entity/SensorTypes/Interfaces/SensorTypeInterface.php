<?php

namespace App\ESPDeviceSensor\Entity\SensorTypes\Interfaces;

use App\ESPDeviceSensor\Entity\Sensor;
use App\UserInterface\Entity\Card\CardView;

interface SensorTypeInterface
{
    public function setSensorObject(Sensor $id);

    public function getSensorTypeID(): int;

    public function getSensorObject(): Sensor;

    public function getSensorTypeName(): string;

    public function getCardViewObject(): ?CardView;

    public function getSensorClass(): string;
}
