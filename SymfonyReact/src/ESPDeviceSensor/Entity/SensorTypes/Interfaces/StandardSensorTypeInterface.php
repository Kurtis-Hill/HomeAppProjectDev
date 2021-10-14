<?php


namespace App\ESPDeviceSensor\Entity\SensorTypes\Interfaces;

use App\Entity\Card\CardView;
use App\ESPDeviceSensor\Entity\Sensors;

Interface StandardSensorTypeInterface
{
    public function getSensorTypeID(): int;

    public function setSensorTypeID(int $id): void;

    public function getSensorNameID(): Sensors;

    public function getCardViewObject(): ?CardView;

    public function setCardViewObject(CardView $cardView): void;
}
