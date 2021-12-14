<?php

namespace App\ESPDeviceSensor\Entity\SensorTypes\Interfaces;

use App\User\Entity\UserInterface\Card\CardView;
use App\ESPDeviceSensor\Entity\Sensor;

interface SensorTypeInterface
{
    public function setSensorObject(Sensor $sensor);

    public function getSensorTypeID(): int;

    public function getSensorObject(): Sensor;

    public function getCardViewObject(): ?CardView;
}
