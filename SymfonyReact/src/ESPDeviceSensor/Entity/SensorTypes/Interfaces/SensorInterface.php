<?php

namespace App\ESPDeviceSensor\Entity\SensorTypes\Interfaces;

use App\Entity\Card\CardView;
use App\ESPDeviceSensor\Entity\Sensors;

interface SensorInterface
{
    public function setSensorObject(Sensors $sensor);

    public function getSensorTypeID(): int;

    public function getSensorObject(): Sensors;

    public function getCardViewObject(): ?CardView;
}
