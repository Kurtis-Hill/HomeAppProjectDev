<?php


namespace App\HomeAppSensorCore\Interfaces;

use App\Entity\Card\CardView;
use App\Entity\Sensors\Sensors;

interface SensorInterface
{
    public function getSensorTypeID(): int;

    public function getSensorObject(): Sensors;

    public function getCardViewObject(): ?CardView;
}
