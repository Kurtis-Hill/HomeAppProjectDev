<?php


namespace App\ESPDeviceSensor\Entity\SensorTypes\Interfaces;

use App\User\Entity\UserInterface\Card\CardView;
use App\ESPDeviceSensor\Entity\Sensor;

Interface StandardSensorTypeInterface
{
    public function getSensorTypeID(): int;

    public function setSensorTypeID(int $id): void;

    public function getSensorNameID(): Sensor;

    public function getCardViewObject(): ?CardView;

    public function setCardViewObject(CardView $cardView): void;
}
